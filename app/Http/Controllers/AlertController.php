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
use App\Services\AlertService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function __construct(private readonly AlertService $alertService) {}

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

        // inactive_member: check whether the member has attendance within the last 30 days
        $inactiveMemberAlerts = $alertsByType->get('inactive_member', collect());
        if ($inactiveMemberAlerts->isNotEmpty()) {
            $memberIds = $inactiveMemberAlerts->pluck('reference_id')->map(fn ($id) => (int) $id);
            $latestRecords = AttendanceRecord::query()
                ->whereIn('member_id', $memberIds)
                ->selectRaw('member_id, MAX(recorded_at) as latest_at')
                ->groupBy('member_id')
                ->pluck('latest_at', 'member_id');

            $cutoff = now()->subDays(30)->startOfDay();
            foreach ($inactiveMemberAlerts as $alert) {
                $mid    = (int) $alert->reference_id;
                $latest = $latestRecords[$mid] ?? null;
                if ($latest === null) {
                    $conditionActive[$alert->id] = true;
                    $conditionDetail[$alert->id] = 'No attendance records in the last 30 days.';
                } else {
                    $latestAt = \Carbon\Carbon::parse($latest);
                    $days     = (int) $latestAt->diffInDays(now());
                    $active   = $latestAt->lt($cutoff);
                    $conditionActive[$alert->id] = $active;
                    $conditionDetail[$alert->id] = $active
                        ? "Last attended {$days} day(s) ago — still inactive."
                        : "Last attended {$days} day(s) ago — condition resolved.";

                    if (! $active && in_array($alert->status, ['open', 'acknowledged'])) {
                        $alert->update(['status' => 'resolved']);
                        $alert->status = 'resolved'; // keep the in-memory object accurate for the view
                        $this->writeTimeline(
                            $mid,
                            'alert_resolved',
                            'Inactive member alert auto-resolved',
                            "Member last attended {$days} day(s) ago — alert closed automatically."
                        );
                    }
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
                $dueDate = $pledge->due_date ? Carbon::parse($pledge->due_date) : null;
                $isOverdue = $dueDate?->isPast() ?? false;

                $conditionActive[$alert->id] = $due > 0 && $isOverdue;
                $conditionDetail[$alert->id] = $conditionActive[$alert->id]
                    ? 'Unpaid balance: Tsh. ' . number_format($due) . ' — condition still active.'
                    : 'Pledge fully paid or no longer overdue — condition resolved.';
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
