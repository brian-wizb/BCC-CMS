<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->with('roles')->orderBy('created_at')->paginate(10),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $attributes = [
            'username' => $data['username'],
            'full_name' => $data['full_name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ];

        if (Schema::hasColumn('users', 'name')) {
            $attributes['name'] = $data['full_name'] ?? $data['username'];
        }

        $user = User::query()->create($attributes);

        $role = Role::query()->where('key', $data['role'])->firstOrFail();
        $user->roles()->sync([$role->id]);

        $this->auditLogger->log(
            request: $request,
            action: 'user.create',
            entityType: 'system_user',
            entityId: $user->id,
            after: [
                'username' => $user->username,
                'role' => $role->key,
                'status' => $user->status,
            ],
        );

        return redirect()->route('users.index')->with('status', 'User created successfully.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $before = [
            'full_name' => $user->full_name,
            'email' => $user->email,
            'status' => $user->status,
            'role' => $user->primaryRole()?->key,
        ];

        $user->fill([
            'full_name' => $data['full_name'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => $data['status'],
        ]);

        if (Schema::hasColumn('users', 'name')) {
            $user->name = $data['full_name'] ?? $user->username;
        }

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $role = Role::query()->where('key', $data['role'])->firstOrFail();
        $user->roles()->sync([$role->id]);

        $this->auditLogger->log(
            request: $request,
            action: 'user.update',
            entityType: 'system_user',
            entityId: $user->id,
            before: $before,
            after: [
                'full_name' => $user->full_name,
                'email' => $user->email,
                'status' => $user->status,
                'role' => $role->key,
            ],
        );

        return redirect()->route('users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if(auth()->id() === $user->id, 422, 'You cannot delete your own account.');

        $before = [
            'username' => $user->username,
            'role' => $user->primaryRole()?->key,
            'status' => $user->status,
        ];

        $user->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'user.delete',
            entityType: 'system_user',
            entityId: $user->id,
            before: $before,
        );

        return redirect()->route('users.index')->with('status', 'User deleted successfully.');
    }
}
