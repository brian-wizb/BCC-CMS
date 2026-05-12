<x-layouts.app title="Pastoral Care Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(251,146,60,0.14);">
                    <i class="fas fa-pray" style="color:rgba(251,146,60,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Pastoral Care Report</h3>
                    <p class="text-xs text-slate-500">Case volume, priority breakdown, and assignee performance</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.pastoral.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.pastoral')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Cases</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($total) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Open</p>
                <p class="mt-1 text-2xl font-bold" style="color:rgba(251,146,60,0.9);">{{ number_format($open) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">In Progress</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-brand-500)]">{{ number_format($inProgress) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Closed / Resolved</p>
                <p class="mt-1 text-2xl font-bold text-emerald-400">{{ number_format($closed) }}</p>
                <p class="mt-1 text-xs text-slate-500">
                    {{ $total > 0 ? round($closed / $total * 100) : 0 }}% resolution rate
                </p>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- By Assignee --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-user-check opacity-60"></i> Performance by Assignee
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Assignee</th>
                                <th class="pb-3 text-right">Total</th>
                                <th class="pb-3 text-right">Resolved</th>
                                <th class="pb-3 text-right">Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @forelse ($byAssignee as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->full_name }}</td>
                                    <td class="py-2.5 text-right">{{ $row->total }}</td>
                                    <td class="py-2.5 text-right text-emerald-400">{{ $row->resolved_count }}</td>
                                    <td class="py-2.5 text-right font-semibold">
                                        {{ $row->total > 0 ? round($row->resolved_count / $row->total * 100) : 0 }}%
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-center text-slate-400">No assignee data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            {{-- By Case Type --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-layer-group opacity-60"></i> By Case Type
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Type</th>
                            <th class="pb-3 text-right">Count</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byType as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucwords(str_replace('_', ' ', $row->case_type)) }}</td>
                                <td class="py-2.5 text-right">{{ $row->total }}</td>
                                <td class="py-2.5 text-right text-slate-500">
                                    {{ $total > 0 ? round($row->total / $total * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">—</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- By Priority --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-exclamation-circle opacity-60"></i> By Priority
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Priority</th>
                            <th class="pb-3 text-right">Count</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byPriority as $priority => $count)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucfirst($priority) }}</td>
                                <td class="py-2.5 text-right">{{ $count }}</td>
                                <td class="py-2.5 text-right text-slate-500">
                                    {{ $total > 0 ? round($count / $total * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">—</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- Monthly Trend --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-chart-line opacity-60"></i> Monthly Trend
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Month</th>
                            <th class="pb-3 text-right">Cases Opened</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($monthlyTrend as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->month }}</td>
                                <td class="py-2.5 text-right">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-slate-400">No trend data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>
        </div>

        {{-- Recent Open Cases --}}
        @if ($recentOpen->isNotEmpty())
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-folder-open opacity-60" style="color:rgba(251,146,60,0.8);"></i> Open Cases
                    <span class="ml-1 rounded-full px-2 py-0.5 text-xs" style="background:rgba(251,146,60,0.15);color:rgba(251,146,60,0.9);">{{ $open + $inProgress }}</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-3 pb-3">Member</th>
                                <th class="px-3 pb-3">Type</th>
                                <th class="px-3 pb-3">Priority</th>
                                <th class="px-3 pb-3">Status</th>
                                <th class="px-3 pb-3">Assigned To</th>
                                <th class="px-3 pb-3">Opened</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @foreach ($recentOpen as $case)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-[var(--color-ink-950)]">{{ $case->member?->full_name ?? '—' }}</td>
                                    <td class="px-3 py-3">{{ ucwords(str_replace('_', ' ', $case->case_type)) }}</td>
                                    <td class="px-3 py-3"><x-ui.status-badge :status="$case->priority" /></td>
                                    <td class="px-3 py-3"><x-ui.status-badge :status="$case->status" /></td>
                                    <td class="px-3 py-3 text-slate-400">{{ $case->assignee?->full_name ?? '—' }}</td>
                                    <td class="px-3 py-3 text-slate-400">{{ optional($case->opened_at)->format('d M Y') ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @endif

    </section>
</x-layouts.app>
