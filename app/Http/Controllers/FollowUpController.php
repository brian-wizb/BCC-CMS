<?php

namespace App\Http\Controllers;

use App\Models\FollowUpHistory;
use App\Models\FollowUpTaskRecipient;
use App\Models\FollowUpTask;
use App\Models\Leader;
use App\Models\Member;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

        return view('follow-up.pipeline', [
            'stages'   => collect($stages)->mapWithKeys(fn ($stage) => [$stage => $visitors->where('status', $stage)->values()]),
            'members'  => $members,
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
            ->with(['leader', 'history', 'recipients'])
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
        ]);
    }

    public function peopleByType(Request $request): JsonResponse
    {
        $type = (string) $request->query('person_type', '');

        if (! in_array($type, ['visitor', 'member'], true)) {
            return response()->json(['data' => []]);
        }

        $people = match ($type) {
            'visitor' => Visitor::query()
                ->orderBy('full_name')
                ->get(['id', 'full_name'])
                ->map(fn ($person) => ['id' => $person->id, 'name' => $person->full_name]),
            'member' => Member::query()
                ->orderBy('full_name')
                ->get(['id', 'full_name'])
                ->map(fn ($person) => ['id' => $person->id, 'name' => $person->full_name]),
            default => collect(),
        };

        return response()->json(['data' => $people->values()]);
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'assignment_scope' => ['required', 'string', Rule::in(['individual', 'multiple'])],
            'person_type' => ['required', 'string', Rule::in(['visitor', 'member'])],
            'person_id'   => ['nullable', 'integer'],
            'person_ids'  => ['nullable'],
            'leader_id'   => ['nullable', 'integer', Rule::exists('leaders', 'id')],
            'task_type'   => ['required', 'string', Rule::in(['call', 'sms', 'visit', 'prayer', 'counseling', 'zone_assignment'])],
            'priority'    => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'due_date'    => ['nullable', 'date'],
            'status'      => ['required', 'string', Rule::in(['pending', 'in_progress', 'completed'])],
            'notes'       => ['nullable', 'string'],
        ]);

        $data['notes'] = trim((string) ($data['notes'] ?? '')) ?: null;
        
        // Parse person_ids - handle both JSON string and array formats
        $rawPersonIds = $data['person_ids'] ?? [];
        if (is_string($rawPersonIds)) {
            $rawPersonIds = json_decode($rawPersonIds, true) ?? [];
        }
        
        $personIds = collect($rawPersonIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($data['assignment_scope'] === 'multiple') {
            if ($personIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'person_ids' => 'Select at least one person for this follow-up task.',
                ]);
            }

            $data['person_id'] = null;
        } else {
            if (empty($data['person_type']) || empty($data['person_id'])) {
                throw ValidationException::withMessages([
                    'person_id' => 'Select a person for this follow-up task.',
                ]);
            }

            $personIds = collect([(int) $data['person_id']]);
        }

        unset($data['assignment_scope']);
        unset($data['person_ids']);

        if (! empty($data['leader_id'])) {
            $leader = Leader::query()->find($data['leader_id']);
            $data['assigned_to'] = $leader?->user_id;
        }

        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $task = FollowUpTask::query()->create($data);

        $recipients = collect();
        $recipientSummaries = [];
        if ($data['person_id'] !== null) {
            $recipients->push([
                'person_type' => $data['person_type'],
                'person_id' => (int) $data['person_id'],
            ]);
        } elseif ($personIds->isNotEmpty()) {
            $recipients = $personIds->map(fn (int $personId) => [
                'person_type' => $data['person_type'],
                'person_id' => $personId,
            ]);
        }

        foreach ($recipients as $recipient) {
            $person = match ($recipient['person_type']) {
                'member' => Member::query()->find($recipient['person_id']),
                'visitor' => Visitor::query()->find($recipient['person_id']),
                default => null,
            };

            if (! $person) {
                continue;
            }

            $displayName = match ($recipient['person_type']) {
                'member' => $person->full_name ?? 'Member #' . $recipient['person_id'],
                'visitor' => $person->full_name ?? 'Visitor #' . $recipient['person_id'],
                default => 'Unknown',
            };

            FollowUpTaskRecipient::query()->create([
                'task_id' => $task->id,
                'person_type' => $recipient['person_type'],
                'person_id' => $recipient['person_id'],
                'display_name' => $displayName,
                'phone' => $person->phone ?? null,
            ]);

            $recipientSummaries[] = $displayName . ($person->phone ? ' (' . $person->phone . ')' : '');
        }

        if ($recipientSummaries !== []) {
            $task->update([
                'notes' => $task->mergeDisplayNotes(trim((string) $task->notes) . PHP_EOL . 'Recipients: ' . implode(', ', $recipientSummaries)),
            ]);
        }

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

        $data['notes'] = $task->mergeDisplayNotes($data['notes'] ?? null);

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
