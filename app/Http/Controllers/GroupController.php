<?php

namespace App\Http\Controllers;

use App\Http\Requests\Groups\StoreGroupMemberRequest;
use App\Http\Requests\Groups\StoreGroupBulkMembersRequest;
use App\Http\Requests\Groups\StoreGroupRequest;
use App\Http\Requests\Groups\UpdateGroupRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Member;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $groups = Group::query()
            ->withCount('memberships')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"))
            ->orderByRaw('is_predefined DESC, name ASC')
            ->paginate(16)
            ->withQueryString();

        return view('groups.index', compact('groups', 'search'));
    }

    public function create(): View
    {
        return view('groups.create', ['group' => new Group()]);
    }

    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $group = Group::query()->create($data);

        $this->auditLogger->log(
            request: $request,
            action: 'group.create',
            entityType: 'group',
            entityId: $group->id,
            after: Arr::only($group->toArray(), ['name', 'slug']),
        );

        return redirect()->route('groups.show', $group)->with('status', 'Group created successfully.');
    }

    public function show(Group $group): View
    {
        $group->loadCount('memberships');
        $group->load(['memberships.member']);

        $existingMemberIds = $group->memberships->pluck('member_id')->filter()->all();

        $availableMembers = Member::query()
            ->whereNotIn('id', $existingMemberIds)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'phone', 'zone', 'tithe_code']);

        return view('groups.show', compact('group', 'availableMembers'));
    }

    public function edit(Group $group): View
    {
        return view('groups.edit', compact('group'));
    }

    public function update(UpdateGroupRequest $request, Group $group): RedirectResponse
    {
        $before = Arr::only($group->toArray(), ['name', 'slug', 'description', 'icon', 'color']);
        $data = $request->validated();
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }
        $group->update($data);

        $this->auditLogger->log(
            request: $request,
            action: 'group.update',
            entityType: 'group',
            entityId: $group->id,
            before: $before,
            after: Arr::only($group->fresh()->toArray(), ['name', 'slug', 'description', 'icon', 'color']),
        );

        return redirect()->route('groups.show', $group)->with('status', 'Group updated successfully.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $before = Arr::only($group->toArray(), ['name', 'slug']);
        $groupId = $group->id;

        DB::transaction(function () use ($group) {
            $group->memberships()->delete();
            $group->delete();
        });

        $this->auditLogger->log(
            request: request(),
            action: 'group.delete',
            entityType: 'group',
            entityId: $groupId,
            before: $before,
        );

        return redirect()->route('groups.index')->with('status', 'Group deleted successfully.');
    }

    public function storeMember(StoreGroupMemberRequest $request, Group $group): RedirectResponse
    {
        $data = $request->validated();

        // Prevent duplicate registered-member entry in same group
        if (! blank($data['member_id'] ?? null)) {
            if ($group->memberships()->where('member_id', $data['member_id'])->exists()) {
                throw ValidationException::withMessages([
                    'member_id' => 'This member is already in this group.',
                ]);
            }
        }

        $group->memberships()->create([
            'member_id'   => $data['member_id'] ?? null,
            'guest_name'  => blank($data['member_id'] ?? null) ? ($data['guest_name'] ?? null) : null,
            'guest_phone' => blank($data['member_id'] ?? null) ? ($data['guest_phone'] ?? null) : null,
            'role'        => $data['role'],
            'joined_at'   => $data['joined_at'] ?? now()->toDateString(),
            'notes'       => $data['notes'] ?? null,
        ]);

        $this->auditLogger->log(
            request: $request,
            action: 'group.member.add',
            entityType: 'group_member',
            after: [
                'group_id'  => $group->id,
                'member_id' => $data['member_id'] ?? null,
                'guest'     => $data['guest_name'] ?? null,
                'role'      => $data['role'],
            ],
        );

        return redirect()->route('groups.show', $group)->with('status', 'Member added to group.');
    }

    public function storeMembersBulk(StoreGroupBulkMembersRequest $request, Group $group): RedirectResponse
    {
        $data = $request->validated();
        $memberIds = collect($data['member_ids'])->map(fn ($id) => (int) $id)->unique()->values();

        $existingIds = $group->memberships()
            ->whereIn('member_id', $memberIds)
            ->pluck('member_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $newIds = $memberIds->reject(fn ($id) => in_array($id, $existingIds, true))->values();

        if ($newIds->isEmpty()) {
            return redirect()->route('groups.show', $group)->with('status', 'Selected members are already in this group.');
        }

        $rows = $newIds->map(function (int $memberId) use ($group, $data): array {
            return [
                'group_id' => $group->id,
                'member_id' => $memberId,
                'guest_name' => null,
                'guest_phone' => null,
                'role' => $data['role'],
                'joined_at' => $data['joined_at'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        GroupMember::query()->insert($rows);

        $this->auditLogger->log(
            request: $request,
            action: 'group.member.bulk_add',
            entityType: 'group_member',
            after: [
                'group_id' => $group->id,
                'added_count' => count($rows),
                'skipped_count' => count($existingIds),
                'role' => $data['role'],
            ],
        );

        $message = count($rows).' member(s) added in bulk.';
        if (count($existingIds) > 0) {
            $message .= ' '.count($existingIds).' already existed and were skipped.';
        }

        return redirect()->route('groups.show', $group)->with('status', $message);
    }

    public function destroyMember(Group $group, GroupMember $membership): RedirectResponse
    {
        abort_unless($membership->group_id === $group->id, 404);

        $before = Arr::only($membership->toArray(), ['group_id', 'member_id', 'guest_name', 'role']);
        $membershipId = $membership->id;
        $membership->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'group.member.remove',
            entityType: 'group_member',
            entityId: $membershipId,
            before: $before,
        );

        return redirect()->route('groups.show', $group)->with('status', 'Member removed from group.');
    }
}
