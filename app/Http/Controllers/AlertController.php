<?php

namespace App\Http\Controllers;

use App\Mail\AlertAssigned;
use App\Models\Alert;
use App\Models\AttendanceRecord;
use App\Models\Leader;
use App\Models\Member;
use App\Models\MemberTimelineEvent;
use App\Models\FollowUpTask;
use App\Models\PastoralCase;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $status   = trim((string) $request->string('status'));
        $severity = trim((string) $request->string('severity'));

        // Fetch all matching alerts, critical-first, grouped by type
        $alertsByType = Alert::query()
            ->with('assignee:id,full_name')
            ->when($status   !== '', fn ($q) => $q->where('status', $status))
            ->when($severity !== '', fn ($q) => $q->where('severity', $severity))
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low')")
            ->orderByDesc('id')
            ->get()
            ->groupBy('alert_type');

        // ── Real-time condition checks ─────────────────────────────────────
        // For each alert we compute (without changing any stored status) whether
        // the underlying problem still actually exists right now. This gives
        // reviewers the context to act correctly.
        //
        // $conditionActive[alert_id] = true  → problem still exists
        // $conditionActive[alert_id] = false → problem no longer exists (safe to resolve)
        // $conditionDetail[alert_id] = string → human-readable live context

        $conditionActive = [];
        $conditionDetail = [];

        // inactive_member: check which members now actually have records
        $inactiveMemberAlerts = $alertsByType->get('inactive_member', collect());
        if ($inactiveMemberAlerts->isNotEmpty()) {
            $memberIds = $inactiveMemberAlerts->pluck('reference_id')->map(fn ($id) => (int) $id);
            // Count per member in one query
            $recordCounts = AttendanceRecord::query()
                ->whereIn('member_id', $memberIds)
                ->selectRaw('member_id, COUNT(*) as cnt')
                ->groupBy('member_id')
                ->pluck('cnt', 'member_id');

            foreach ($inactiveMemberAlerts as $alert) {
                $mid   = (int) $alert->reference_id;
                $count = (int) ($recordCounts[$mid] ?? 0);
                $conditionActive[$alert->id] = $count === 0;
                $conditionDetail[$alert->id] = $count === 0
                    ? 'Still no attendance records on file.'
                    : "Member now has {$count} attendance record(s) — condition resolved.";

                // Auto-sync: if condition is gone but alert is still open/acknowledged,
                // resolve it immediately so status and reality are never out of step.
                if ($count > 0 && in_array($alert->status, ['open', 'acknowledged'])) {
                    $alert->update(['status' => 'resolved']);
                    $alert->status = 'resolved'; // keep the in-memory object accurate for the view
                    $this->writeTimeline(
                        $mid,
                        'alert_resolved',
                        'Inactive member alert auto-resolved',
                        "Member now has {$count} attendance record(s) — alert closed automatically."
                    );
                }
            }
        }

        // pastoral_case_overdue: check whether case is still unresolved
        $pastoralAlerts = $alertsByType->get('pastoral_case_overdue', collect());
        if ($pastoralAlerts->isNotEmpty()) {
            $caseIds   = $pastoralAlerts->pluck('reference_id')->map(fn ($id) => (int) $id);
            $openCases = PastoralCase::query()
                ->whereIn('id', $caseIds)
                ->whereIn('status', ['open', 'in_progress'])
                ->pluck('status', 'id');

            foreach ($pastoralAlerts as $alert) {
                $cid    = (int) $alert->reference_id;
                $active = $openCases->has($cid);
                $conditionActive[$alert->id] = $active;
                $conditionDetail[$alert->id] = $active
                    ? 'Pastoral case is still open/in-progress.'
                    : 'Pastoral case has been closed — condition resolved.';
            }
        }

        // prayer_request_stale: check whether prayer-support care request is still unresolved
        $prayerAlerts = $alertsByType->get('prayer_request_stale', collect());
        if ($prayerAlerts->isNotEmpty()) {
            $prIds  = $prayerAlerts->pluck('reference_id')->map(fn ($id) => (int) $id);
            $openPR = PastoralCase::query()
                ->whereIn('id', $prIds)
                ->where('case_type', 'prayer_support')
                ->whereIn('status', ['open', 'in_progress'])
                ->pluck('status', 'id');

            foreach ($prayerAlerts as $alert) {
                $prid   = (int) $alert->reference_id;
                $active = $openPR->has($prid);
                $conditionActive[$alert->id] = $active;
                $conditionDetail[$alert->id] = $active
                    ? 'Prayer support request is still open/in-progress.'
                    : 'Prayer support request has been answered or closed — condition resolved.';
            }
        }

        // lapsed_attendance: check whether member still hasn't attended in 60+ days
        $lapsedAlerts = $alertsByType->get('lapsed_attendance', collect());
        if ($lapsedAlerts->isNotEmpty()) {
            $memberIds    = $lapsedAlerts->pluck('reference_id')->map(fn ($id) => (int) $id);
            $latestRecord = AttendanceRecord::query()
                ->whereIn('member_id', $memberIds)
                ->selectRaw('member_id, MAX(recorded_at) as latest_at')
                ->groupBy('member_id')
                ->pluck('latest_at', 'member_id');

            $cutoff = now()->subDays(60);
            foreach ($lapsedAlerts as $alert) {
                $mid    = (int) $alert->reference_id;
                $latest = $latestRecord[$mid] ?? null;
                if ($latest === null) {
                    $conditionActive[$alert->id] = true;
                    $conditionDetail[$alert->id] = 'No attendance records found — condition still active.';
                } else {
                    $latestAt = \Carbon\Carbon::parse($latest);
                    $days     = (int) $latestAt->diffInDays(now());
                    $active   = $latestAt->lt($cutoff);
                    $conditionActive[$alert->id] = $active;
                    $conditionDetail[$alert->id] = $active
                        ? "Last attended {$days} day(s) ago — still lapsed."
                        : "Member attended {$days} day(s) ago — condition resolved.";
                }
            }
        }

        // follow_up_overdue: check whether the follow-up task is still pending and past due
        $followUpAlerts = $alertsByType->get('follow_up_overdue', collect());
        if ($followUpAlerts->isNotEmpty()) {
            $taskIds   = $followUpAlerts->pluck('reference_id')->map(fn ($id) => (int) $id);
            $openTasks = FollowUpTask::query()
                ->whereIn('id', $taskIds)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('due_date', '<', today())
                ->pluck('status', 'id');

            foreach ($followUpAlerts as $alert) {
                $tid    = (int) $alert->reference_id;
                $active = $openTasks->has($tid);
                $conditionActive[$alert->id] = $active;
                $conditionDetail[$alert->id] = $active
                    ? 'Follow-up task is still overdue and incomplete.'
                    : 'Follow-up task has been completed or is no longer overdue.';
            }
        }

        // pledge_due: check actual unpaid balance (due_date past + payments < amount)
        $pledgeAlerts = $alertsByType->get('pledge_due', collect());
        if ($pledgeAlerts->isNotEmpty()) {
            $pledgeIds = $pledgeAlerts->pluck('reference_id')->map(fn ($id) => (int) $id);
            $pledges   = \App\Models\Pledge::with('payments')->whereIn('id', $pledgeIds)->get()->keyBy('id');

            foreach ($pledgeAlerts as $alert) {
                $pid    = (int) $alert->reference_id;
                $pledge = $pledges->get($pid);
                if (! $pledge) {
                    $conditionActive[$alert->id] = false;
                    $conditionDetail[$alert->id] = 'Pledge record no longer exists — condition resolved.';
                    continue;
                }
                $paid = $pledge->payments->sum('amount');
                $due  = $pledge->amount - $paid;
                $conditionActive[$alert->id] = $due > 0;
                $conditionDetail[$alert->id] = $due > 0
                    ? 'Unpaid balance: Tsh. ' . number_format($due) . ' — condition still active.'
                    : 'Pledge fully paid — condition resolved.';
            }
        }

        $now = now();

        return view('alerts.index', [
            'alertsByType'      => $alertsByType,
            'status'            => $status,
            'severity'          => $severity,
            'leaders'           => Leader::query()->orderBy('full_name')->get(['id', 'full_name']),
            'openCount'         => Alert::query()->where('status', 'open')->count(),
            'acknowledgedCount' => Alert::query()->where('status', 'acknowledged')->count(),
            'criticalCount'     => Alert::query()->where('severity', 'critical')->whereIn('status', ['open', 'acknowledged'])->count(),
            'overdueCount'      => Alert::query()->where('due_at', '<', $now)->whereIn('status', ['open', 'acknowledged'])->count(),
            'conditionActive'   => $conditionActive,
            'conditionDetail'   => $conditionDetail,
        ]);
    }

    public function run(): RedirectResponse
    {
        $created = $this->generateAlerts();

        return redirect()
            ->route('alerts.index')
            ->with('status', 'Alert generation completed. Created ' . $created . ' new alert(s).');
    }

    /**
     * Core alert generation logic — shared by the manual Run button and the Artisan command.
     *
     * Rules:
     *  - Statuses are NEVER auto-changed here; reviewers manage those manually.
     *    Exception: stale inactive_member alerts where the member now has records
     *    are auto-resolved (the condition objectively no longer exists).
     *  - A new alert is only created when no open OR acknowledged alert already
     *    exists for the same reference, preventing daily duplicates on re-runs.
     *  - Acknowledged alerts sitting unresolved for 7+ days are auto-escalated
     *    one severity level before new alerts are generated.
     */
    public function generateAlerts(): int
    {
        $created = 0;

        // ── Cleanup: auto-resolve stale inactive_member alerts ─────────────
        // If a member now has attendance records the "inactive" condition no
        // longer exists. Resolve those open/acknowledged alerts automatically
        // so they don't mislead reviewers.
        Alert::query()
            ->where('alert_type', 'inactive_member')
            ->whereIn('status', ['open', 'acknowledged'])
            ->each(function (Alert $alert) {
                $hasRecords = AttendanceRecord::where('member_id', (int) $alert->reference_id)->exists();
                if ($hasRecords) {
                    $alert->update(['status' => 'resolved']);
                    $this->writeTimeline(
                        (int) $alert->reference_id,
                        'alert_resolved',
                        'Inactive member alert auto-resolved',
                        'Member now has attendance records on file — alert closed automatically.'
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

        // 1. Inactive members — no attendance records at all
        Member::query()
            ->whereDoesntHave('attendanceRecords')
            ->orderBy('id')
            ->each(function (Member $member) use (&$created, $alertExists) {
                if ($alertExists('inactive_member', 'member', (string) $member->id)) {
                    return;
                }
                Alert::create([
                    'alert_type'     => 'inactive_member',
                    'reference_type' => 'member',
                    'reference_id'   => (string) $member->id,
                    'title'          => 'Inactive member: ' . $member->full_name,
                    'message'        => $member->full_name . ' has no attendance records on file.',
                    'severity'       => 'medium',
                    'status'         => 'open',
                ]);
                $created++;
                $this->writeTimeline($member->id, 'alert_created', 'Inactive member alert opened', 'Member has no attendance records on file.');
            });

        // 2. Lapsed attendance — attended before but not in the last 60+ days
        AttendanceRecord::query()
            ->selectRaw('member_id, MAX(recorded_at) as latest_at')
            ->groupBy('member_id')
            ->havingRaw('latest_at <= ?', [now()->subDays(60)->toDateTimeString()])
            ->each(function ($row) use (&$created, $alertExists) {
                $member = Member::find($row->member_id);
                if (! $member) {
                    return;
                }
                if ($alertExists('lapsed_attendance', 'member', (string) $member->id)) {
                    return;
                }
                $days = (int) now()->diffInDays($row->latest_at);
                Alert::create([
                    'alert_type'     => 'lapsed_attendance',
                    'reference_type' => 'member',
                    'reference_id'   => (string) $member->id,
                    'title'          => 'Lapsed attendance: ' . $member->full_name,
                    'message'        => $member->full_name . " last attended {$days} days ago (over 60 days without attendance).",
                    'severity'       => $days >= 90 ? 'high' : 'medium',
                    'status'         => 'open',
                    'due_at'         => now()->addDays(14),
                ]);
                $created++;
                $this->writeTimeline($member->id, 'alert_created', 'Lapsed attendance alert opened', "Member has not attended in {$days} days.");
            });

        // 3. Overdue pastoral cases — open and not closed after 30 days
        PastoralCase::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->where('opened_at', '<=', now()->subDays(30))
            ->each(function (PastoralCase $case) use (&$created, $alertExists) {
                if ($alertExists('pastoral_case_overdue', 'pastoral_case', (string) $case->id)) {
                    return;
                }
                Alert::create([
                    'alert_type'     => 'pastoral_case_overdue',
                    'reference_type' => 'pastoral_case',
                    'reference_id'   => (string) $case->id,
                    'title'          => 'Overdue pastoral case: ' . $case->summary,
                    'message'        => 'Pastoral case #' . $case->id . ' has been open for over 30 days without resolution.',
                    'severity'       => 'high',
                    'status'         => 'open',
                    'due_at'         => now()->addDays(7),
                ]);
                $created++;
                if ($case->member_id) {
                    $this->writeTimeline($case->member_id, 'alert_created', 'Overdue pastoral case alert', 'Case has been open for over 30 days.');
                }
            });

        // 4. Stale prayer support requests — open/in-progress and older than 30 days
        PastoralCase::query()
            ->where('case_type', 'prayer_support')
            ->whereIn('status', ['open', 'in_progress'])
            ->where('opened_at', '<=', now()->subDays(30))
            ->each(function (PastoralCase $pr) use (&$created, $alertExists) {
                if ($alertExists('prayer_request_stale', 'prayer_request', (string) $pr->id)) {
                    return;
                }
                Alert::create([
                    'alert_type'     => 'prayer_request_stale',
                    'reference_type' => 'pastoral_case',
                    'reference_id'   => (string) $pr->id,
                    'title'          => 'Stale prayer support request #' . $pr->id,
                    'message'        => 'A prayer support request has had no response or status update in over 30 days.',
                    'severity'       => 'medium',
                    'status'         => 'open',
                    'due_at'         => now()->addDays(7),
                ]);
                $created++;
                if ($pr->member_id) {
                    $this->writeTimeline($pr->member_id, 'alert_created', 'Stale prayer request alert', 'Prayer support request #' . $pr->id . ' has been open for over 30 days.');
                }
            });

        // 5. Overdue follow-up tasks — incomplete tasks past their due date
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

        // 6. Pledges due — past due_date with unpaid balance
        \App\Models\Pledge::query()
            ->with('payments')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->each(function (\App\Models\Pledge $pledge) use (&$created, $alertExists) {
                $paid = $pledge->payments->sum('amount');
                $due  = $pledge->amount - $paid;
                if ($due <= 0) {
                    return; // fully paid
                }
                if ($alertExists('pledge_due', 'pledge', (string) $pledge->id)) {
                    return;
                }
                $name = $pledge->pledger_name ?? 'Unknown pledger';
                $days = (int) now()->diffInDays($pledge->due_date);
                Alert::create([
                    'alert_type'     => 'pledge_due',
                    'reference_type' => 'pledge',
                    'reference_id'   => (string) $pledge->id,
                    'title'          => "Pledge due: {$name}",
                    'message'        => "{$name} has an outstanding pledge balance of Tsh. " . number_format($due) . " — due {$days} day(s) ago.",
                    'severity'       => $days >= 30 ? 'high' : 'medium',
                    'status'         => 'open',
                    'due_at'         => now()->addDays(7),
                ]);
                $created++;
            });

        return $created;
    }

    public function update(Request $request, Alert $alert): RedirectResponse
    {
        $data = $request->validate([
            'status'      => ['required', 'string', Rule::in(['open', 'acknowledged', 'resolved'])],
            'severity'    => ['required', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],
            'assigned_to' => ['nullable', 'integer', Rule::exists('leaders', 'id')],
            'due_at'      => ['nullable', 'date'],
        ]);

        $oldStatus   = $alert->status;
        $oldAssignee = $alert->assigned_to;

        $alert->update($data);

        // Write timeline event when an alert for a member is resolved
        if ($oldStatus !== 'resolved' && $data['status'] === 'resolved'
            && $alert->reference_type === 'member' && $alert->reference_id
        ) {
            $this->writeTimeline(
                (int) $alert->reference_id,
                'alert_resolved',
                'Alert resolved: ' . $alert->title,
                'Alert was marked as resolved.'
            );
        }

        // Send email notification when assignee is newly set or changed
        $newAssignee = $data['assigned_to'] ?? null;
        if ($newAssignee && (string) $oldAssignee !== (string) $newAssignee) {
            $leader = Leader::find($newAssignee);
            if ($leader && $leader->email) {
                try {
                    Mail::to($leader->email)->send(new AlertAssigned($alert, $leader));
                } catch (\Throwable) {
                    // Email is best-effort; do not block the update
                }
            }
        }

        return back()->with('status', 'Alert updated successfully.');
    }

    public function destroy(Alert $alert): RedirectResponse
    {
        $alert->delete();

        return redirect()->route('alerts.index')->with('status', 'Alert deleted successfully.');
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
