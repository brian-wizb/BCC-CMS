<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FollowUpHistory;
use App\Models\FollowUpTask;
use App\Models\Leader;
use App\Models\Member;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(): View
    {
        return view('follow-up.index');
    }

    public function pipeline(): View
    {
        $stages   = ['new', 'contacted', 'counseled', 'joined_zone', 'in_class', 'converted'];
        $visitors = Visitor::query()->orderByDesc('id')->get();
        $members  = Member::query()->with('followUpTasks')->orderBy('full_name')->get(['id', 'full_name', 'phone', 'zone']);
        $families = Family::query()->with('followUpTasks')->orderBy('head_of_family')->get(['id', 'head_of_family', 'phone', 'zone']);

        return view('follow-up.pipeline', [
            'stages'   => collect($stages)->mapWithKeys(fn ($stage) => [$stage => $visitors->where('status', $stage)->values()]),
            'members'  => $members,
            'families' => $families,
        ]);
    }

    public function tasks(Request $request): View
    {
        $status   = trim((string) $request->string('status'));
        $priority = trim((string) $request->string('priority'));
        $type     = trim((string) $request->string('type'));

        $user = $request->user();
        $leader = $user?->leader;

        $tasks = FollowUpTask::query()
            ->with(['leader', 'history'])
            ->when($status !== '',   fn ($q) => $q->where('status',    $status))
            ->when($priority !== '', fn ($q) => $q->where('priority',  $priority))
            ->when($type !== '',     fn ($q) => $q->where('task_type', $type))
            ->when(
                $user->hasRole('counsellor'),
                fn ($q) => $leader
                    ? $q->where('leader_id', $leader->id)
                    : $q->whereRaw('0 = 1')
            )
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('follow-up.tasks', [
            'tasks'    => $tasks,
            'status'   => $status,
            'priority' => $priority,
            'type'     => $type,
            'leaders'  => Leader::query()->where('status', 'active')->orderBy('full_name')->get(['id', 'full_name', 'role']),
            'members'  => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
            'visitors' => Visitor::query()->orderBy('full_name')->get(['id', 'full_name']),
            'families' => Family::query()->orderBy('head_of_family')->get(['id', 'head_of_family']),
        ]);
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'person_type' => ['required', 'string', Rule::in(['visitor', 'member', 'family'])],
            'person_id'   => ['required', 'integer'],
            'leader_id'   => ['nullable', 'integer', Rule::exists('leaders', 'id')],
            'task_type'   => ['required', 'string', Rule::in(['call', 'sms', 'visit', 'prayer', 'counseling', 'zone_assignment'])],
            'priority'    => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'due_date'    => ['nullable', 'date'],
            'status'      => ['required', 'string', Rule::in(['pending', 'in_progress', 'completed'])],
            'notes'       => ['nullable', 'string'],
        ]);

        if (! empty($data['leader_id'])) {
            $leader = Leader::query()->find($data['leader_id']);
            $data['assigned_to'] = $leader?->user_id;
        }

        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        FollowUpTask::query()->create($data);

        return back()->with('status', 'Follow-up task created successfully.');
    }

    public function updateTask(Request $request, FollowUpTask $task): RedirectResponse
    {
        $data = $request->validate([
            'leader_id' => ['nullable', 'integer', Rule::exists('leaders', 'id')],
            'priority'  => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'status'    => ['required', 'string', Rule::in(['pending', 'in_progress', 'completed'])],
            'notes'     => ['nullable', 'string'],
            'due_date'  => ['nullable', 'date'],
        ]);

        if (! empty($data['leader_id'])) {
            $leader = Leader::query()->find($data['leader_id']);
            $data['assigned_to'] = $leader?->user_id;
        } else {
            $data['assigned_to'] = null;
        }

        $data['completed_at'] = $data['status'] === 'completed' ? now() : null;
        $task->update($data);

        return back()->with('status', 'Follow-up task updated successfully.');
    }

    public function destroyTask(FollowUpTask $task): RedirectResponse
    {
        $task->delete();

        return back()->with('status', 'Follow-up task deleted successfully.');
    }

    public function storeHistory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'task_id' => ['required', 'integer', Rule::exists('follow_up_tasks', 'id')],
            'action_taken' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        FollowUpHistory::query()->create([
            'task_id' => $data['task_id'],
            'action_taken' => $data['action_taken'],
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return back()->with('status', 'Follow-up history recorded successfully.');
    }
}
