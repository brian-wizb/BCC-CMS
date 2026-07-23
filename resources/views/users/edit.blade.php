<x-layouts.app title="Edit User">
    <section class="surface-card p-6 users-responsive">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-user-edit text-base" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Users</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit user</h3>
                </div>
            </div>
            <a href="{{ route('users.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back to users
            </a>
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data" class="space-y-5" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-1">
                <div class="flex items-center gap-5">
                    <div id="photo-preview-wrap" class="relative h-20 w-20 flex-shrink-0">
                        @if ($user->profile_photo_path)
                            <img id="photo-preview" src="{{ route('users.profile-photo', $user) }}" alt="{{ $user->full_name ?: $user->username }}"
                                 class="absolute inset-0 h-20 w-20 rounded-2xl object-cover" />
                            <div id="photo-initials"
                                 class="hidden h-20 w-20 items-center justify-center rounded-2xl text-xl font-bold text-white"
                                 style="background:linear-gradient(135deg,rgba(99,102,241,0.85),rgba(168,85,247,0.75));">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->username, 0, 2)) }}
                            </div>
                        @else
                            <div id="photo-initials"
                                 class="flex h-20 w-20 items-center justify-center rounded-2xl text-xl font-bold text-white"
                                 style="background:linear-gradient(135deg,rgba(99,102,241,0.85),rgba(168,85,247,0.75));">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->username, 0, 2)) }}
                            </div>
                            <img id="photo-preview" src="" alt="Preview"
                                 class="absolute inset-0 hidden h-20 w-20 rounded-2xl object-cover" />
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="form-label mb-1 block" for="profile_photo">Profile Photo <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp"
                               class="form-input cursor-pointer text-sm"
                               onchange="
                                   const f = this.files[0];
                                   if (f) {
                                       const url = URL.createObjectURL(f);
                                       const img = document.getElementById('photo-preview');
                                       const ini = document.getElementById('photo-initials');
                                       img.src = url;
                                       img.classList.remove('hidden');
                                       ini.classList.add('hidden');
                                   }
                               ">
                        @error('profile_photo')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-400">JPG, PNG or WebP, max 2 MB. Replaces existing photo.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="username">Username</label>
                    <input id="username" class="form-input" value="{{ $user->username }}" disabled>
                </div>
                <div>
                    <label class="form-label" for="full_name">Full Name</label>
                    <input id="full_name" name="full_name" class="form-input" value="{{ old('full_name', $user->full_name) }}">
                    @error('full_name')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $user->email) }}">
                    @error('email')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="role">Role <span class="text-rose-400">*</span></label>
                    <select id="role" name="role" class="form-input" required @disabled(!auth()->user()->hasPermission('users.assign_roles'))>
                        @foreach ($roles as $role)
                            <option value="{{ $role->key }}" @selected(old('role', $user->primaryRole()?->key) === $role->key)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @if (!auth()->user()->hasPermission('users.assign_roles'))
                        <p class="mt-1 text-xs text-amber-400"><i class="fas fa-lock mr-1"></i>You cannot change roles.</p>
                        <input type="hidden" name="role" value="{{ old('role', $user->primaryRole()?->key) }}">
                    @endif
                    @error('role')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="status">Status <span class="text-rose-400">*</span></label>
                    <select id="status" name="status" class="form-input" required>
                        <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="password">New Password <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input id="password" name="password" type="password" class="form-input" autocomplete="new-password">
                    @error('password')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password">
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-[var(--color-surface-200)] pt-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary flex items-center gap-1.5">
                    <i class="fas fa-save text-sm"></i> Save changes
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>
