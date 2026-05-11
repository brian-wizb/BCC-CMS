<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departments\StoreDepartmentMemberRequest;
use App\Http\Requests\Departments\StoreDepartmentRequest;
use App\Http\Requests\Departments\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\DepartmentMember;
use App\Models\Leader;
use App\Models\Member;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status'));

        $departments = Department::query()
            ->with('leader')
            ->withCount('memberships')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('departments.index', [
            'departments' => $departments,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('departments.create', [
            'department' => new Department(['status' => 'active']),
            'leaders' => Leader::query()->where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::query()->create($request->validated());

        $this->auditLogger->log(
            request: $request,
            action: 'department.create',
            entityType: 'department',
            entityId: $department->id,
            after: Arr::only($department->toArray(), ['name', 'leader_id', 'status']),
        );

        return redirect()->route('departments.show', $department)->with('status', 'Department created successfully.');
    }

    public function show(Department $department): View
    {
        $department->load([
            'leader',
            'memberships.member',
        ]);

        return view('departments.show', [
            'department' => $department,
            'availableMembers' => Member::query()
                ->whereNotIn('id', $department->memberships->pluck('member_id'))
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'phone', 'zone']),
        ]);
    }

    public function edit(Department $department): View
    {
        return view('departments.edit', [
            'department' => $department,
            'leaders' => Leader::query()->where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $before = Arr::only($department->toArray(), ['name', 'leader_id', 'status', 'description']);
        $department->update($request->validated());

        $this->auditLogger->log(
            request: $request,
            action: 'department.update',
            entityType: 'department',
            entityId: $department->id,
            before: $before,
            after: Arr::only($department->fresh()->toArray(), ['name', 'leader_id', 'status', 'description']),
        );

        return redirect()->route('departments.show', $department)->with('status', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $before = Arr::only($department->toArray(), ['name', 'leader_id', 'status']);
        $departmentId = $department->id;
        $department->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'department.delete',
            entityType: 'department',
            entityId: $departmentId,
            before: $before,
        );

        return redirect()->route('departments.index')->with('status', 'Department deleted successfully.');
    }

    public function storeMember(StoreDepartmentMemberRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();

        if ($department->memberships()->where('member_id', $data['member_id'])->exists()) {
            throw ValidationException::withMessages([
                'member_id' => 'This member already belongs to the department.',
            ]);
        }

        $membership = $department->memberships()->create([
            'member_id' => $data['member_id'],
            'role' => $data['role'] ?? 'member',
            'status' => $data['status'],
            'joined_at' => now(),
        ]);

        $this->auditLogger->log(
            request: $request,
            action: 'department.member.add',
            entityType: 'department_member',
            entityId: $membership->id,
            after: [
                'department_id' => $department->id,
                'member_id' => $membership->member_id,
                'role' => $membership->role,
                'status' => $membership->status,
            ],
        );

        return redirect()->route('departments.show', $department)->with('status', 'Member assigned to department.');
    }

    public function destroyMember(Department $department, DepartmentMember $membership): RedirectResponse
    {
        abort_unless($membership->department_id === $department->id, 404);

        $before = Arr::only($membership->toArray(), ['department_id', 'member_id', 'role', 'status']);
        $membershipId = $membership->id;
        $membership->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'department.member.remove',
            entityType: 'department_member',
            entityId: $membershipId,
            before: $before,
        );

        return redirect()->route('departments.show', $department)->with('status', 'Member removed from department.');
    }
}
