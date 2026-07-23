<x-layouts.app title="Users">
    <section class="space-y-5 users-responsive">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-users-cog" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">System Users</h3>
                    <p class="text-xs text-slate-500">All system access accounts are created and managed by the administrator.</p>
                </div>
            </div>
            @if (auth()->user()->hasPermission('users.create'))
                <a href="{{ route('users.create') }}"
                    class="btn-primary flex items-center gap-2">
                    <i class="fas fa-user-plus text-sm"></i> New user
                </a>
            @endif
        </div>

        {{-- Search / filter bar --}}
        <form method="GET" action="{{ route('users.index') }}" class="surface-card p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="form-label text-xs">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input name="search" class="form-input pl-8" placeholder="Username, name or email…" value="{{ $search ?? '' }}">
                    </div>
                </div>
                <button type="submit" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                @if ($search ?? false)
                    <a href="{{ route('users.index') }}" class="btn-secondary flex items-center gap-1.5 text-rose-400">
                        <i class="fas fa-times text-xs"></i> Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- Users table --}}
        <div class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Last login</th>
                            <th class="px-4 py-3">Created</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($users as $user)
                            <tr class="hover:bg-[var(--color-surface-50)] transition-colors">
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-[var(--color-ink-950)]">{{ $user->username }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $user->full_name ?: '—' }}</p>
                                    @if ($user->email)
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $user->email }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-ui.status-badge :status="$user->primaryRole()?->name ?? 'Unassigned'" tone="info" />
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-ui.status-badge :status="$user->status" />
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">
                                    {{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (auth()->user()->hasPermission('users.update'))
                                            <a href="{{ route('users.edit', $user) }}"
                                                class="btn-secondary px-2.5 py-1.5 text-xs flex items-center gap-1">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('users.delete') && auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                                data-confirm="Remove user {{ $user->username }}? They will be soft-deleted and can be restored.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary px-2.5 py-1.5 text-xs text-rose-400 flex items-center gap-1">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                                    @if ($search ?? false)
                                        No users found matching "{{ $search }}".
                                    @else
                                        No users yet. Create the first one.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[var(--color-surface-200)] px-4 py-4">
                {{ $users->links() }}
            </div>
        </div>

    </section>
</x-layouts.app>
