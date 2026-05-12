<x-layouts.app title="My Profile">
    <section class="space-y-6 max-w-2xl">

        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                <i class="fas fa-user-circle" style="color:rgba(99,102,241,0.9);"></i>
            </span>
            <div>
                <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">My Profile</h3>
                <p class="text-xs text-slate-500">Update your personal details and change your password</p>
            </div>
        </div>

        {{-- Profile Details --}}
        <article class="surface-card p-6 space-y-4">
            <h4 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Account Details</h4>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="username">Username</label>
                        <input id="username" class="form-input bg-[var(--color-surface-100)] cursor-not-allowed opacity-60"
                               value="{{ $user->username }}" disabled>
                        <p class="mt-1 text-xs text-slate-500">Username cannot be changed.</p>
                    </div>
                    <div>
                        <label class="form-label" for="role">Role</label>
                        <input id="role" class="form-input bg-[var(--color-surface-100)] cursor-not-allowed opacity-60"
                               value="{{ $user->primaryRole()?->name ?? '—' }}" disabled>
                        <p class="mt-1 text-xs text-slate-500">Role is managed by administrators.</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="full_name">Full Name</label>
                        <input id="full_name" name="full_name" class="form-input" value="{{ old('full_name', $user->full_name) }}">
                        @error('full_name')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="phone">Phone</label>
                        <input id="phone" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $user->email) }}">
                    @error('email')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Save changes</button>
                    <span class="text-xs text-slate-500">Last login: {{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}</span>
                </div>
            </form>
        </article>

        {{-- Change Password --}}
        <article class="surface-card p-6 space-y-4">
            <h4 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Change Password</h4>
            <p class="text-xs text-slate-500">Password must be at least 8 characters with mixed case and a number.</p>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label" for="current_password">Current Password</label>
                    <input id="current_password" name="current_password" type="password" class="form-input" autocomplete="current-password" required>
                    @error('current_password')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="password">New Password</label>
                        <input id="password" name="password" type="password" class="form-input" autocomplete="new-password" required>
                        @error('password')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Update password</button>
            </form>
        </article>

    </section>
</x-layouts.app>
