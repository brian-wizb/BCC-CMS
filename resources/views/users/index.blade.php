<x-layouts.app title="Users">
    <section class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        <article class="surface-card p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Create user</p>
            <h3 class="mt-3 text-xl font-semibold text-[var(--color-ink-950)]">Add a system access account</h3>

            <form method="POST" action="{{ route('users.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="form-label" for="username">Username</label>
                    <input id="username" name="username" class="form-input" value="{{ old('username') }}" required>
                </div>

                <div>
                    <label class="form-label" for="full_name">Full name</label>
                    <input id="full_name" name="full_name" class="form-input" value="{{ old('full_name') }}">
                </div>

                <div>
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email') }}">
                </div>

                <div>
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-input" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->key }}" @selected(old('role') === $role->key)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" for="password">Password</label>
                    <input id="password" name="password" type="password" class="form-input" required>
                </div>

                <button type="submit" class="btn-primary w-full">Create user</button>
            </form>
        </article>

        <article class="surface-card overflow-hidden">
            <div class="border-b border-[var(--color-surface-200)] px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">System access</p>
                <h3 class="mt-2 text-xl font-semibold text-[var(--color-ink-950)]">Manage existing users</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Last login</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @foreach ($users as $user)
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-[var(--color-ink-950)]">{{ $user->username }}</p>
                                    <p class="mt-1 text-slate-500">{{ $user->full_name ?: 'No full name' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $user->email ?: 'No email' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <x-ui.status-badge :status="$user->primaryRole()?->name ?? 'Unassigned'" tone="info" />
                                </td>
                                <td class="px-4 py-4">
                                    <x-ui.status-badge :status="$user->status" />
                                </td>
                                <td class="px-4 py-4 text-slate-500">
                                    {{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}
                                </td>
                                <td class="px-4 py-4">
                                    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')

                                        <input type="text" name="full_name" class="form-input" value="{{ $user->full_name }}" placeholder="Full name">

                                        <input type="email" name="email" class="form-input" value="{{ $user->email }}" placeholder="Email">

                                        <select name="role" class="form-input">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->key }}" @selected($user->primaryRole()?->id === $role->id)>{{ $role->name }}</option>
                                            @endforeach
                                        </select>

                                        <select name="status" class="form-input">
                                            <option value="active" @selected($user->status === 'active')>Active</option>
                                            <option value="inactive" @selected($user->status === 'inactive')>Inactive</option>
                                        </select>

                                        <input type="password" name="password" class="form-input" placeholder="New password (optional)">

                                        <div class="flex gap-3">
                                            <button type="submit" class="btn-secondary">Save</button>
                                    </form>

                                    @if (auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary text-red-600">Delete</button>
                                        </form>
                                    @endif
                                        </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[var(--color-surface-200)] px-6 py-4">
                {{ $users->links() }}
            </div>
        </article>
    </section>
</x-layouts.app>
