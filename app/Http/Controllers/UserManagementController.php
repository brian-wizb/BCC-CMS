<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Leader;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $data = $this->attachUploadedProfilePhoto($request, $request->validated());

        $user = User::query()->create([
            'username'  => $data['username'],
            'full_name' => $data['full_name'] ?? null,
            'profile_photo_path' => $data['profile_photo_path'] ?? null,
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

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function profilePhoto(User $user): StreamedResponse|RedirectResponse
    {
        $path = $this->extractPublicProfilePhotoPath($user->profile_photo_path);

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return redirect()->away('https://ui-avatars.com/api/?name=' . urlencode($user->full_name ?: $user->username ?: 'User') . '&background=e2e8f0&color=475569&size=256');
        }

        return Storage::disk('public')->response($path);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data   = $this->attachUploadedProfilePhoto($request, $request->validated(), $user);
        $before = [
            'full_name' => $user->full_name,
            'email'     => $user->email,
            'status'    => $user->status,
            'role'      => $user->primaryRole()?->key,
        ];

        $user->fill([
            'full_name' => $data['full_name'] ?? null,
            'profile_photo_path' => $data['profile_photo_path'] ?? $user->profile_photo_path,
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

    private function attachUploadedProfilePhoto(Request $request, array $data, ?User $user = null): array
    {
        if (! $request->hasFile('profile_photo')) {
            return $data;
        }

        $storedPath = $request->file('profile_photo')->store('users/profile-photos', 'public');
        $data['profile_photo_path'] = $storedPath;

        $existingPath = $this->extractPublicProfilePhotoPath($user?->profile_photo_path);

        if ($existingPath && Str::startsWith($existingPath, 'users/profile-photos/')) {
            Storage::disk('public')->delete($existingPath);
        }

        return $data;
    }

    private function extractPublicProfilePhotoPath(?string $profilePhotoPath): ?string
    {
        if (blank($profilePhotoPath)) {
            return null;
        }

        $path = str_replace('\\', '/', $profilePhotoPath);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $path = (string) parse_url($path, PHP_URL_PATH);
        }

        if (Str::startsWith($path, 'users/profile-photos/')) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return ltrim(Str::after($path, 'storage/'), '/');
        }

        if (! Str::contains($path, '/storage/')) {
            return null;
        }

        return ltrim(Str::after($path, '/storage/'), '/');
    }
}
