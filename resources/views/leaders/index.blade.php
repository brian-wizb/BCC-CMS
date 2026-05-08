<x-layouts.app title="Leaders">
    <section class="surface-card p-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl"
                      style="background:rgba(36,184,255,0.12);">
                    <i class="fas fa-user-shield text-sm" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Leadership</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">
                        Leaders
                        <span class="ml-1.5 rounded-full px-2 py-0.5 text-sm font-medium"
                              style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">{{ $leaders->total() }}</span>
                    </h3>
                </div>
            </div>
            <a href="{{ route('leaders.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> Add leader
            </a>
        </div>

        {{-- Search + Status filter --}}
        <form method="GET" action="{{ route('leaders.index') }}"
              class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input name="search" class="form-input pl-8 text-sm" value="{{ $search }}"
                       placeholder="Search name, role, zone…">
            </div>
            <select name="status" class="form-input w-40 text-sm">
                <option value="">All statuses</option>
                <option value="active"   @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
            <button type="submit" class="btn-secondary text-sm">Filter</button>
        </form>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">
                    <tr>
                        <th class="px-5 py-3"><i class="fas fa-user-tie mr-1.5 opacity-60"></i>Name</th>
                        <th class="px-5 py-3"><i class="fas fa-tag mr-1.5 opacity-60"></i>Role</th>
                        <th class="px-5 py-3"><i class="fas fa-map-marker-alt mr-1.5 opacity-60"></i>Zone</th>
                        <th class="px-5 py-3"><i class="fas fa-phone mr-1.5 opacity-60"></i>Phone</th>
                        <th class="px-5 py-3"><i class="fas fa-toggle-on mr-1.5 opacity-60"></i>Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)]">
                    @forelse ($leaders as $leader)
                        <tr class="transition hover:bg-[var(--color-surface-50)]">
                            <td class="px-5 py-3">
                                <span class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                          style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">
                                        {{ mb_strtoupper(mb_substr($leader->full_name, 0, 1)) }}
                                    </span>
                                    <span class="font-medium text-[var(--color-ink-950)]">{{ $leader->full_name }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $leader->role ?: '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $leader->zone ?: '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $leader->phone ?: '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($leader->status === 'active')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background:rgba(52,211,153,0.12); color:rgba(52,211,153,0.9);">
                                        <i class="fas fa-circle text-[8px]"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold text-slate-400"
                                          style="background:rgba(100,116,139,0.10);">
                                        <i class="fas fa-circle text-[8px]"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('leaders.show', $leader) }}"
                                       class="btn-secondary flex items-center gap-1 px-2.5 py-1 text-xs">
                                        <i class="fas fa-eye text-[10px]"></i> View
                                    </a>
                                    <a href="{{ route('leaders.edit', $leader) }}"
                                       class="btn-secondary flex items-center gap-1 px-2.5 py-1 text-xs">
                                        <i class="fas fa-pen text-[10px]"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('leaders.destroy', $leader) }}"
                                          onsubmit="return confirm('Delete this leader?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-secondary flex items-center gap-1 px-2.5 py-1 text-xs text-red-500 hover:text-red-600">
                                            <i class="fas fa-trash text-[10px]"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center">
                                <i class="fas fa-user-shield mb-2 block text-3xl text-slate-200"></i>
                                <p class="text-sm text-slate-400">No leaders found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $leaders->links() }}</div>
    </section>
</x-layouts.app>
