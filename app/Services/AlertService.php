<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AttendanceRecord;
use App\Models\FollowUpTask;
use App\Models\Member;
use App\Models\MemberTimelineEvent;
use App\Models\Pledge;
use App\Models\Visitor;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertAssigned;
use Carbon\Carbon;

class AlertService
{
    /**
     * Auto-generate and update alerts based on current system state.
     * Called whenever relevant data changes (attendance, pastoral cases, follow-ups, etc.).
     */
    public function generateAlerts(): int
    {
        $created = 0;

        // ── Cleanup: auto-resolve stale inactive_member alerts ─────────────
        // If a member has attendance within the last 30 days the "inactive"
        // condition no longer exists. Resolve those open/acknowledged alerts.
        $inactiveCutoff = now()->subDays(30)->startOfDay();
        Alert::query()
            ->where('alert_type', 'inactive_member')
            ->whereIn('status', ['open', 'acknowledged'])
            ->each(function (Alert $alert) use ($inactiveCutoff) {
                $recentAttendance = AttendanceRecord::where('member_id', (int) $alert->reference_id)
                    ->where('recorded_at', '>=', $inactiveCutoff)
                    ->exists();
                if ($recentAttendance) {
                    $alert->update(['status' => 'resolved']);
                    $this->writeTimeline(
                        (int) $alert->reference_id,
                        'alert_resolved',
                        'Inactive member alert auto-resolved',
                        'Member has attendance within the last 30 days — alert closed automatically.'
                    );
                }
            });

        // ── Auto-escalate: acknowledged alerts sitting 7+ days → bump severity ──
        $escalateMap = ['low' => 'medium', 'medium' => 'high', 'high' => 'critical'];
        Alert::query()
            ->where('status', 'acknowledged')
            ->where('updated_at', '<=', now()->subDays(7))
            ->whereIn('severity', array_keys($escalateMap))
            ->each(function (Alert $alert) use ($escalateMap) {
                $newSeverity = $escalateMap[$alert->severity];
                $alert->update(['severity' => $newSeverity]);
                if ($alert->reference_type === 'member' && $alert->reference_id) {
                    $this->writeTimeline(
                        (int) $alert->reference_id,
                        'alert_escalated',
                        'Alert escalated to ' . $newSeverity . ': ' . $alert->title,
                        'Alert has been acknowledged for over 7 days without resolution.'
                    );
                }
            });

        // ── Duplicate-safe existence check ────────────────────────────────────
        // Returns true when an open OR acknowledged alert already exists for the
        // given type + reference, so the daily run never creates duplicates.
        $alertExists = fn (string $type, string $refType, string $refId): bool =>
            Alert::query()
                ->where('alert_type', $type)
                ->where('reference_type', $refType)
                ->where('reference_id', $refId)
                ->whereIn('status', ['open', 'acknowledged'])
                ->exists();

        // ── Auto-resolve stale pledge_due alerts when the pledge is no longer overdue or fully paid
        Alert::query()
            ->where('alert_type', 'pledge_due')
            ->whereIn('status', ['open', 'acknowledged'])
            ->each(function (Alert $alert) {
                $pledge = Pledge::with('payments')->find((int) $alert->reference_id);
                if (! $pledge) {
                    $alert->update(['status' => 'resolved']);
                    return;
                }

                $paid = $pledge->payments->sum('amount');
                $due = $pledge->amount - $paid;
                $dueDate = $pledge->due_date ? Carbon::parse($pledge->due_date) : null;

                if ($due <= 0 || ! $dueDate || ! $dueDate->isPast()) {
                    $alert->update(['status' => 'resolved']);
                    return;
                }

                $days = (int) now()->diffInDays($dueDate);
                $message = ($pledge->pledger_name ?? 'Unknown pledger') . ' has an outstanding pledge balance of Tsh. ' . number_format($due) . " — due {$days} day(s) ago.";
                $severity = $days >= 30 ? 'high' : 'medium';

                if ($alert->message !== $message || $alert->severity !== $severity) {
                    $alert->update([
                        'message'  => $message,
                        'severity' => $severity,
                        'due_at'   => now()->addDays(7),
                    ]);
                }
            });

        // 1. Inactive members — no attendance within the last 30 days
        $inactiveCutoff = now()->subDays(30)->startOfDay();
        Member::query()
            ->whereDoesntHave('attendanceRecords', fn ($q) => $q->where('recorded_at', '>=', $inactiveCutoff))
            ->orderBy('id')
            ->each(function (Member $member) use (&$created, $alertExists, $inactiveCutoff) {
                if ($alertExists('inactive_member', 'member', (string) $member->id)) {
                    return;
                }

                $latestAt = AttendanceRecord::where('member_id', $member->id)
                    ->orderByDesc('recorded_at')
                    ->value('recorded_at');
                $days = $latestAt ? (int) now()->diffInDays($latestAt) : null;

                if ($latestAt) {
                    $message = $member->full_name . " last attended {$days} days ago.";
                    $details = "Member last attended {$days} day(s) ago — no attendance in the last 30 days.";
                } else {
                    $message = $member->full_name . ' has no attendance records on file.';
                    $details = 'Member has no attendance records on file.';
                }

                Alert::create([
                    'alert_type'     => 'inactive_member',
                    'reference_type' => 'member',
                    'reference_id'   => (string) $member->id,
                    'title'          => 'Inactive member: ' . $member->full_name,
                    'message'        => $message,
                    'severity'       => 'medium',
                    'status'         => 'open',
                ]);
                $created++;
                $this->writeTimeline($member->id, 'alert_created', 'Inactive member alert opened', $details);
            });

        // 2. Overdue follow-up tasks — incomplete tasks past their due date
        $overdueTasks = FollowUpTask::query()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->get();

        if ($overdueTasks->isNotEmpty()) {
            $memberNames  = Member::whereIn('id', $overdueTasks->where('person_type', 'member')->pluck('person_id'))
                ->pluck('full_name', 'id');
            $visitorNames = Visitor::whereIn('id', $overdueTasks->where('person_type', 'visitor')->pluck('person_id'))
                ->pluck('full_name', 'id');

            foreach ($overdueTasks as $task) {
                if ($alertExists('follow_up_overdue', 'follow_up_task', (string) $task->id)) {
                    continue;
                }
                $personName = match ($task->person_type) {
                    'member'  => $memberNames[$task->person_id]  ?? 'Member #' . $task->person_id,
                    'visitor' => $visitorNames[$task->person_id] ?? 'Visitor #' . $task->person_id,
                    default   => ucfirst($task->person_type) . ' #' . $task->person_id,
                };
                $taskLabel = ucwords(str_replace('_', ' ', $task->task_type ?? 'task'));
                $days      = (int) now()->diffInDays($task->due_date);
                $notesPart = $task->notes ? ' — ' . $task->notes : '';
                Alert::create([
                    'alert_type'     => 'follow_up_overdue',
                    'reference_type' => 'follow_up_task',
                    'reference_id'   => (string) $task->id,
                    'title'          => "{$taskLabel} for {$personName}",
                    'message'        => "{$taskLabel} for {$personName}{$notesPart}. Task is {$days} day(s) overdue.",
                    'severity'       => $days >= 14 ? 'high' : 'medium',
                    'status'         => 'open',
                    'due_at'         => now()->addDays(3),
                ]);
                $created++;
            }
        }

        // 3. Pledges due — past due_date with unpaid balance
        Pledge::query()
            ->with('payments')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->each(function (Pledge $pledge) use (&$created, $alertExists) {
                $paid = $pledge->payments->sum('amount');
                $due  = $pledge->amount - $paid;
                if ($due <= 0) {
                    return; // fully paid
                }

                $name = $pledge->pledger_name ?? 'Unknown pledger';
                $days = (int) now()->diffInDays($pledge->due_date);
                $message = "{$name} has an outstanding pledge balance of Tsh. " . number_format($due) . " — due {$days} day(s) ago.";
                $severity = $days >= 30 ? 'high' : 'medium';

                if ($alertExists('pledge_due', 'pledge', (string) $pledge->id)) {
                    $existingAlert = Alert::query()
                        ->where('alert_type', 'pledge_due')
                        ->where('reference_type', 'pledge')
                        ->where('reference_id', (string) $pledge->id)
                        ->whereIn('status', ['open', 'acknowledged'])
                        ->first();

                    if ($existingAlert) {
                        $existingAlert->update([
                            'message'  => $message,
                            'severity' => $severity,
                            'due_at'   => now()->addDays(7),
                        ]);
                    }

                    return;
                }

                Alert::create([
                    'alert_type'     => 'pledge_due',
                    'reference_type' => 'pledge',
                    'reference_id'   => (string) $pledge->id,
                    'title'          => "Pledge due: {$name}",
                    'message'        => $message,
                    'severity'       => $severity,
                    'status'         => 'open',
                    'due_at'         => now()->addDays(7),
                ]);
                $created++;
            });

        return $created;
    }

    private function writeTimeline(int $memberId, string $eventType, string $title, string $details): void
    {
        MemberTimelineEvent::create([
            'member_id'  => $memberId,
            'event_type' => $eventType,
            'event_date' => now(),
            'title'      => $title,
            'details'    => $details,
        ]);
    }
}
