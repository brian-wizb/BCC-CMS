<?php

namespace App\Http\Controllers;

use App\Models\Leader;
use App\Models\Member;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeaderController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status'));

        $leaders = Leader::query()
            ->with('member')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('zone', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();

        return view('leaders.index', [
            'leaders' => $leaders,
            'search'  => $search,
            'status'  => $status,
        ]);
    }

    public function create(): View
    {
        return view('leaders.create', [
            'leader'  => new Leader(['status' => 'active']),
            'members' => Member::orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data   = $this->validatedData($request);
        $leader = Leader::query()->create($data);

        $this->auditLogger->log(
            request: $request,
            action: 'leader.create',
            entityType: 'leader',
            entityId: $leader->id,
            after: Arr::only($leader->toArray(), ['full_name', 'role', 'zone', 'status']),
        );

        return redirect()->route('leaders.show', $leader)->with('status', 'Leader created successfully.');
    }

    public function show(Leader $leader): View
    {
        $leader->load(['member', 'followUpTasks' => fn ($q) => $q->latest('id')->take(10)]);

        return view('leaders.show', compact('leader'));
    }

    public function edit(Leader $leader): View
    {
        return view('leaders.edit', [
            'leader'  => $leader,
            'members' => Member::orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }

    public function update(Request $request, Leader $leader): RedirectResponse
    {
        $before = Arr::only($leader->toArray(), ['full_name', 'role', 'zone', 'status']);
        $leader->update($this->validatedData($request));

        $this->auditLogger->log(
            request: $request,
            action: 'leader.update',
            entityType: 'leader',
            entityId: $leader->id,
            before: $before,
            after: Arr::only($leader->fresh()->toArray(), ['full_name', 'role', 'zone', 'status']),
        );

        return redirect()->route('leaders.show', $leader)->with('status', 'Leader updated successfully.');
    }

    public function destroy(Leader $leader): RedirectResponse
    {
        $before = Arr::only($leader->toArray(), ['full_name', 'role', 'zone', 'status']);
        $id     = $leader->id;
        $leader->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'leader.delete',
            entityType: 'leader',
            entityId: $id,
            before: $before,
        );

        return redirect()->route('leaders.index')->with('status', 'Leader deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['nullable', 'integer', 'exists:members,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'email'     => ['nullable', 'email', 'max:255'],
            'role'      => ['nullable', 'string', 'max:255'],
            'zone'      => ['nullable', 'string', 'max:255'],
            'status'    => ['required', 'string', Rule::in(['active', 'inactive'])],
            'notes'     => ['nullable', 'string'],
        ]);
    }
}
