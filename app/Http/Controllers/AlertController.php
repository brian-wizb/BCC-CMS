<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->string('status'));

        $alerts = Alert::query()
            ->with('assignee:id,full_name,username')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('alerts.index', [
            'alerts' => $alerts,
            'status' => $status,
            'users' => User::query()->orderBy('full_name')->get(['id', 'full_name', 'username']),
        ]);
    }

    public function run(): RedirectResponse
    {
        $inactiveMembers = Member::query()
            ->whereDoesntHave('attendanceRecords')
            ->orderBy('id')
            ->limit(20)
            ->get(['id', 'full_name']);

        $created = 0;

        foreach ($inactiveMembers as $member) {
            $alert = Alert::query()->firstOrCreate(
                [
                    'alert_type' => 'inactive_member',
                    'reference_type' => 'member',
                    'reference_id' => (string) $member->id,
                    'status' => 'open',
                ],
                [
                    'title' => 'Inactive member: '.$member->full_name,
                    'message' => $member->full_name.' has no attendance records yet.',
                    'severity' => 'medium',
                ]
            );

            if ($alert->wasRecentlyCreated) {
                $created++;
            }
        }

        return redirect()
            ->route('alerts.index')
            ->with('status', 'Alert generation completed. Created '.$created.' new alert(s).');
    }

    public function update(Request $request, Alert $alert): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(['open', 'acknowledged', 'resolved'])],
            'severity' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'due_at' => ['nullable', 'date'],
        ]);

        $alert->update($data);

        return back()->with('status', 'Alert updated successfully.');
    }

    public function destroy(Alert $alert): RedirectResponse
    {
        $alert->delete();

        return redirect()->route('alerts.index')->with('status', 'Alert deleted successfully.');
    }
}
