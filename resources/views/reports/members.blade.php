<x-layouts.app title="Members Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.14);">
                    <i class="fas fa-users" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Members Report</h3>
                    <p class="text-xs text-slate-500">Membership growth, distribution, and profile breakdown</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.members.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.members')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Members</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($total) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $dateFrom || $dateTo ? 'In selected period' : 'All time' }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Born Again</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($bornAgain) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $total > 0 ? round($bornAgain / $total * 100, 1) : 0 }}% of members</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Baptized</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($baptized) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $total > 0 ? round($baptized / $total * 100, 1) : 0 }}% of members</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Holy Spirit Baptised</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($holySpirit) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $total > 0 ? round($holySpirit / $total * 100, 1) : 0 }}% of members</p>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- By Gender --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-venus-mars opacity-60"></i> By Gender
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Gender</th>
                            <th class="pb-3 text-right">Count</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byGender as $gender => $count)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucfirst($gender ?: 'Unspecified') }}</td>
                                <td class="py-2.5 text-right">{{ number_format($count) }}</td>
                                <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round($count / $total * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- By Marital Status --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-heart opacity-60"></i> By Marital Status
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
                        @forelse ($byMarital as $status => $count)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucfirst($status ?: 'Unspecified') }}</td>
                                <td class="py-2.5 text-right">{{ number_format($count) }}</td>
                                <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round($count / $total * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>
        </div>

        {{-- By Zone --}}
        <article class="surface-card p-6">
            <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                <i class="fas fa-map-marked-alt opacity-60"></i> By Zone
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Zone</th>
                            <th class="pb-3 text-right">Members</th>
                            <th class="pb-3 text-right">%</th>
                            <th class="pb-3 w-2/5">Distribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @php $maxZone = $byZone->max('total') ?: 1; @endphp
                        @forelse ($byZone as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->zone ?: 'Unassigned' }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->total) }}</td>
                                <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round($row->total / $total * 100, 1) : 0 }}%</td>
                                <td class="py-2.5">
                                    <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                        <div class="h-full rounded-full" style="width:{{ round($row->total / $maxZone * 100) }}%;background:linear-gradient(90deg,rgba(36,184,255,0.7),rgba(52,211,153,0.7));"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-400">No zone data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        {{-- Monthly growth --}}
        @if ($monthlyGrowth->isNotEmpty())
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-chart-line opacity-60"></i> Monthly New Members
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Month</th>
                                <th class="pb-3 text-right">New Members</th>
                                <th class="pb-3 w-2/5">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @php $maxMonth = $monthlyGrowth->max('total') ?: 1; @endphp
                            @foreach ($monthlyGrowth as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</td>
                                    <td class="py-2.5 text-right">{{ number_format($row->total) }}</td>
                                    <td class="py-2.5">
                                        <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                            <div class="h-full rounded-full" style="width:{{ round($row->total / $maxMonth * 100) }}%;background:linear-gradient(90deg,rgba(36,184,255,0.8),rgba(52,211,153,0.8));"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @endif

    </section>
</x-layouts.app>
