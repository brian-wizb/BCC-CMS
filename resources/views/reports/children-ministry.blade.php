<x-layouts.app title="Children Ministry Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-child" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Children Ministry Report</h3>
                    <p class="text-xs text-slate-500">Registered children, parent contacts, and linked members overview</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.children-ministry.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <button onclick="window.print()" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-print text-xs"></i> Print
                </button>
                <a href="{{ route('reports.index') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs"></i> All reports
                </a>
            </div>
        </div>

        <x-ui.date-range-filters :action="route('reports.children-ministry')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Children</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($total) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">With Linked Parent</p>
                <p class="mt-1 text-3xl font-bold text-emerald-400">{{ number_format($withLinkedParents) }}</p>
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                    <div class="h-full rounded-full" style="width:{{ $total > 0 ? round($withLinkedParents / $total * 100) : 0 }}%;background:linear-gradient(90deg,rgba(52,211,153,0.8),rgba(36,184,255,0.8));"></div>
                </div>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Male Children</p>
                @php
                    $maleCount = $bySex->get('Male') ?? 0;
                @endphp
                <p class="mt-1 text-3xl font-bold text-blue-400">{{ number_format($maleCount) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Female Children</p>
                @php
                    $femaleCount = $bySex->get('Female') ?? 0;
                @endphp
                <p class="mt-1 text-3xl font-bold text-pink-400">{{ number_format($femaleCount) }}</p>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- By sex --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-venus-mars opacity-60"></i> By Sex
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Sex</th>
                            <th class="pb-3 text-right">Count</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($bySex as $sex => $count)
                            <tr>
                                <td class="py-2.5">
                                    <span class="inline-block rounded-full px-2.5 py-1 text-xs font-medium"
                                        @if (strtolower($sex) === 'male')
                                            style="background:rgba(59,130,246,0.12); color:rgba(59,130,246,0.9);"
                                        @else
                                            style="background:rgba(236,72,153,0.12); color:rgba(236,72,153,0.9);"
                                        @endif
                                    >
                                        {{ $sex }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right">{{ number_format($count) }}</td>
                                <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round($count / $total * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">—</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- Parent Linkage --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-link opacity-60"></i> Parent Linkage Status
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Count</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        <tr>
                            <td class="py-2.5">With Linked Parent</td>
                            <td class="py-2.5 text-right">{{ number_format($withLinkedParents) }}</td>
                            <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round($withLinkedParents / $total * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td class="py-2.5">Without Linked Parent</td>
                            <td class="py-2.5 text-right">{{ number_format($total - $withLinkedParents) }}</td>
                            <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round(($total - $withLinkedParents) / $total * 100, 1) : 0 }}%</td>
                        </tr>
                    </tbody>
                </table>
            </article>
        </div>

        {{-- Recent Children Table --}}
        <article class="surface-card p-6">
            <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                <i class="fas fa-list opacity-60"></i> Recently Added Children
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500 border-b border-[var(--color-surface-200)]">
                        <tr>
                            <th class="pb-3">Child Name</th>
                            <th class="pb-3">Date of Birth</th>
                            <th class="pb-3">Sex</th>
                            <th class="pb-3">Parent Name</th>
                            <th class="pb-3">Parent Contact</th>
                            <th class="pb-3">Linked Member</th>
                            <th class="pb-3">Added</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($recentChildren as $child)
                            <tr class="hover:bg-[var(--color-surface-50)] transition">
                                <td class="py-2.5">{{ $child->full_name }}</td>
                                <td class="py-2.5">{{ optional($child->date_of_birth)->format('d M Y') ?: '—' }}</td>
                                <td class="py-2.5">
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
                                <td class="py-2.5">{{ $child->parent_name ?: '—' }}</td>
                                <td class="py-2.5">{{ $child->parent_contact ?: '—' }}</td>
                                <td class="py-2.5">
                                    @if ($child->parentMember)
                                        <a href="{{ route('members.show', $child->parentMember) }}" class="text-[var(--color-brand-600)] hover:underline">
                                            {{ $child->parentMember->full_name }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-2.5 text-slate-500">{{ optional($child->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-slate-400">No children found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

    </section>
</x-layouts.app>
