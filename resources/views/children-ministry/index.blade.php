<x-layouts.app title="Children Ministry">
    <section class="surface-card p-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.12);">
                    <i class="fas fa-child text-lg" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Children Ministry</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Registered Children
                        <span class="ml-1.5 rounded-full px-2 py-0.5 text-xs font-medium" style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">{{ $children->total() }}</span>
                    </h3>
                </div>
            </div>

            <div class="print-hide flex flex-wrap gap-2">
                <a href="{{ route('children-ministry.create') }}" class="btn-primary flex items-center gap-1.5">
                    <i class="fas fa-plus text-xs"></i> Add child
                </a>
                <a href="{{ route('reports.children-ministry') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-chart-bar text-xs"></i> Report
                </a>
                <a href="{{ route('children-ministry.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <button type="button" onclick="window.print()" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-print text-xs"></i> Print
                </button>
            </div>
        </div>

        {{-- Search + Filters --}}
        <div class="print-hide mt-5 grid gap-4">
            <form method="GET" action="{{ route('children-ministry.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div class="relative xl:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input name="search" class="form-input pl-8" placeholder="Search by child name, parent name, or contact" value="{{ $search }}">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">From</label>
                    <input type="date" name="date_from" class="form-input" value="{{ $dateFrom }}">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">To</label>
                    <input type="date" name="date_to" class="form-input" value="{{ $dateTo }}">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-secondary flex items-center gap-1.5 flex-1">
                        <i class="fas fa-filter text-xs"></i> Filter
                    </button>
                </div>
                @if ($search || $dateFrom || $dateTo)
                    <div class="flex items-end gap-2">
                        <a href="{{ route('children-ministry.index') }}" class="btn-secondary flex items-center gap-1.5 flex-1">
                            <i class="fas fa-times text-xs"></i> Clear
                        </a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto rounded-lg border border-[var(--color-surface-200)]">
            <table class="w-full text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-[var(--color-surface-200)] bg-[var(--color-surface-50)]">
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Child Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Date of Birth</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Sex</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Parent Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Contact</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Added</th>
                        <th class="print-hide px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)]">
                    @forelse ($children as $child)
                        <tr class="hover:bg-[var(--color-surface-50)] transition">
                            <td class="print-hide px-4 py-3">
                                <div>
                                    <p class="font-medium text-[var(--color-ink-950)]">{{ $child->full_name ?: 'Unnamed' }}</p>
                                    @if ($child->parentMember)
                                        <p class="text-xs text-slate-400">Parent: {{ $child->parentMember->full_name }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{ optional($child->date_of_birth)->format('d M Y') ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-medium"
                                    @if ($child->sex === 'Male')
                                        style="background:rgba(59,130,246,0.12); color:rgba(59,130,246,0.9);"
                                    @else
                                        style="background:rgba(236,72,153,0.12); color:rgba(236,72,153,0.9);"
                                    @endif
                                >
                                    {{ $child->sex ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $child->parent_name ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $child->parent_contact ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ $child->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    <a href="{{ route('children-ministry.show', $child) }}" class="btn-secondary flex items-center gap-1 py-1 px-2.5 text-xs">
                                        <i class="fas fa-eye text-[10px]"></i> View
                                    </a>
                                    <a href="{{ route('children-ministry.edit', $child) }}" class="btn-secondary flex items-center gap-1 py-1 px-2.5 text-xs">
                                        <i class="fas fa-pen text-[10px]"></i> Edit
                                    </a>
                                    @can('children_ministry.delete')
                                        <form method="POST" action="{{ route('children-ministry.destroy', $child) }}" data-confirm="Delete {{ $child->full_name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary flex items-center gap-1 py-1 px-2.5 text-xs text-red-500 hover:bg-red-50">
                                                <i class="fas fa-trash text-[10px]"></i> Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <i class="fas fa-user-slash mb-2 block text-2xl text-slate-300"></i>
                                <p class="text-slate-400">No children registered yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $children->links() }}
        </div>
    </section>
</x-layouts.app>
