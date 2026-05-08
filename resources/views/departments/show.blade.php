<x-layouts.app title="Department Details">
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <article class="surface-card p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Department</p>
                    <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $department->name }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ $department->description ?: 'No description recorded.' }}</p>
                </div>

                <div class="flex gap-3">
                    <x-ui.status-badge :status="$department->status" />
                    <a href="{{ route('departments.edit', $department) }}" class="btn-secondary">Edit</a>
                </div>
            </div>

            <dl class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Leader</dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)]">{{ $department->leader?->full_name ?: $department->leader?->username ?: '—' }}</dd>
                </div>
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Members</dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)]">{{ $department->memberships->count() }}</dd>
                </div>
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Created</dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)]">{{ $department->created_at?->format('d M Y') }}</dd>
                </div>
            </dl>

            <div class="mt-8 overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Member</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Zone</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Joined</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse ($department->memberships->sortBy(fn ($membership) => $membership->member?->full_name) as $membership)
                            <tr>
                                <td class="px-4 py-4 font-medium text-[var(--color-ink-950)]">{{ $membership->member?->full_name ?: '—' }}</td>
                                <td class="px-4 py-4 text-slate-500">{{ $membership->member?->phone ?: '—' }}</td>
                                <td class="px-4 py-4 text-slate-500">{{ $membership->member?->zone ?: '—' }}</td>
                                <td class="px-4 py-4 text-slate-500">{{ $membership->role }}</td>
                                <td class="px-4 py-4"><x-ui.status-badge :status="$membership->status" /></td>
                                <td class="px-4 py-4 text-slate-500">{{ $membership->joined_at?->format('d M Y') ?: '—' }}</td>
                                <td class="px-4 py-4">
                                    <form method="POST" action="{{ route('departments.members.destroy', [$department, $membership]) }}" onsubmit="return confirm('Remove this member from the department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary text-red-600">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">No members assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="surface-card p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Assign member</p>
            <h4 class="mt-2 text-xl font-semibold text-[var(--color-ink-950)]">Add department member</h4>

            <form method="POST" action="{{ route('departments.members.store', $department) }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="member_id" class="form-label">Member</label>
                    <select id="member_id" name="member_id" class="form-input mt-2" required>
                        <option value="">Select member</option>
                        @foreach ($availableMembers as $member)
                            <option value="{{ $member->id }}" @selected((string) old('member_id') === (string) $member->id)>
                                {{ $member->full_name }}{{ $member->zone ? ' - '.$member->zone : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('member_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="form-label">Role</label>
                    <input id="role" name="role" class="form-input mt-2" value="{{ old('role', 'member') }}" placeholder="member">
                    @error('role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-input mt-2" required>
                        <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary w-full">Assign member</button>
            </form>
        </aside>
    </section>
</x-layouts.app>
