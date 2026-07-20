<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Leader;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(): View
    {
        $search = request('search');

        return view('users.index', [
            'users' => User::query()
                ->with('roles')
                ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                }))
                ->orderBy('created_at')
                ->paginate(15)
                ->withQueryString(),
            'roles'  => Role::query()->orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $leaders = Leader::query()
            ->with('member:id,full_name')
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get(['id', 'member_id', 'user_id', 'full_name']);

        return view('users.create', [
            'roles'   => Role::query()->orderBy('name')->get(),
            'leaders' => $leaders,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'username'  => $data['username'],
            'full_name' => $data['full_name'] ?? null,
            'email'     => $data['email'] ?? null,
            'password'  => Hash::make($data['password']),
            'status'    => 'active',
        ]);

        $role = Role::query()->where('key', $data['role'])->firstOrFail();
        $user->roles()->sync([$role->id]);

        if (! empty($data['leader_id'])) {
            Leader::query()->where('id', $data['leader_id'])->update(['user_id' => $user->id]);
        }

        $this->auditLogger->log(
            request: $request,
            action: 'user.create',
            entityType: 'system_user',
            entityId: $user->id,
            after: [
                'username' => $user->username,
                'role'     => $role->key,
                'status'   => $user->status,
                'leader_id' => $data['leader_id'] ?? null,
            ],
        );

        return redirect()->route('users.index')->with('status', 'User created successfully.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data   = $request->validated();
        $before = [
            'full_name' => $user->full_name,
            'email'     => $user->email,
            'status'    => $user->status,
            'role'      => $user->primaryRole()?->key,
        ];

        $user->fill([
            'full_name' => $data['full_name'] ?? null,
            'email'     => $data['email'] ?? null,
            'status'    => $data['status'],
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $this->auditLogger->log(
                request: $request,
                action: 'user.password_changed',
                entityType: 'system_user',
                entityId: $user->id,
            );
        }

        $user->save();

        // Only allow role change if the actor has assign_roles permission
        $newRole = Role::query()->where('key', $data['role'])->firstOrFail();
        if ($before['role'] !== $newRole->key) {
            abort_unless(auth()->user()->hasPermission('users.assign_roles'), 403, 'You are not allowed to change user roles.');
            $user->roles()->sync([$newRole->id]);
        }

        $this->auditLogger->log(
            request: $request,
            action: 'user.update',
            entityType: 'system_user',
            entityId: $user->id,
            before: $before,
            after: [
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'status'    => $user->status,
                'role'      => $newRole->key,
            ],
        );

        return redirect()->route('users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if(auth()->id() === $user->id, 422, 'You cannot delete your own account.');

        $before = [
            'username' => $user->username,
            'role'     => $user->primaryRole()?->key,
            'status'   => $user->status,
        ];

        // Soft delete — preserves audit log FK references
        $user->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'user.delete',
            entityType: 'system_user',
            entityId: $user->id,
            before: $before,
        );

        return redirect()->route('users.index')->with('status', 'User deactivated and removed.');
    }

    public function restore(User $user): RedirectResponse
    {
        $user->restore();

        $this->auditLogger->log(
            request: request(),
            action: 'user.restore',
            entityType: 'system_user',
            entityId: $user->id,
            after: ['username' => $user->username],
        );

        return redirect()->route('users.index')->with('status', 'User restored successfully.');
    }
}
