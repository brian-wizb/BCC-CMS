<x-layouts.app title="Pledges Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-hand-holding-usd" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Pledges & Giving Report</h3>
                    <p class="text-xs text-slate-500">Pledge fulfillment, collections, and outstanding balances</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.pledges.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.pledges')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Summary stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Pledged</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">TSh {{ number_format($totalPledged, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Collected</p>
                <p class="mt-1 text-2xl font-bold text-emerald-400">TSh {{ number_format($totalCollected, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Outstanding</p>
                <p class="mt-1 text-2xl font-bold {{ $outstanding > 0 ? 'text-rose-400' : 'text-emerald-400' }}">TSh {{ number_format($outstanding, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Fulfillment Rate</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ $fulfillmentRate }}%</p>
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                    <div class="h-full rounded-full transition-all" style="width:{{ $fulfillmentRate }}%;background:linear-gradient(90deg,rgba(52,211,153,0.8),rgba(36,184,255,0.8));"></div>
                </div>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- By type --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-tags opacity-60"></i> By Pledge Type
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Type</th>
                            <th class="pb-3 text-right">Pledges</th>
                            <th class="pb-3 text-right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byType as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucfirst($row->pledge_type ?: 'General') }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->count) }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">No pledges.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- By zone --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-map-marked-alt opacity-60"></i> By Zone
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Zone</th>
                            <th class="pb-3 text-right">Pledges</th>
                            <th class="pb-3 text-right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byZone as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->zone ?: 'Unassigned' }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->count) }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">No zone data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>
        </div>

        {{-- Monthly pledge trend --}}
        @if ($monthlyPledges->isNotEmpty())
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-chart-bar opacity-60"></i> Monthly Pledge Trend
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Month</th>
                                <th class="pb-3 text-right">Amount (KES)</th>
                                <th class="pb-3 w-2/5">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @php $maxPledge = $monthlyPledges->max('total') ?: 1; @endphp
                            @foreach ($monthlyPledges as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</td>
                                    <td class="py-2.5 text-right">{{ number_format($row->total, 2) }}</td>
                                    <td class="py-2.5">
                                        <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                            <div class="h-full rounded-full" style="width:{{ round($row->total / $maxPledge * 100) }}%;background:linear-gradient(90deg,rgba(244,193,93,0.8),rgba(255,111,145,0.8));"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @endif

        {{-- Recent pledges --}}
        <article class="surface-card p-6">
            <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                <i class="fas fa-list-alt opacity-60"></i> Recent Pledges
                <span class="ml-1 text-xs font-normal text-slate-500">(latest 20)</span>
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 pb-3">Pledger</th>
                            <th class="px-3 pb-3">Type</th>
                            <th class="px-3 pb-3 text-right">Pledged</th>
                            <th class="px-3 pb-3 text-right">Collected</th>
                            <th class="px-3 pb-3 text-right">Outstanding</th>
                            <th class="px-3 pb-3">Pledge Date</th>
                            <th class="px-3 pb-3">Due Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($recentPledges as $pledge)
                            @php
                                $collected   = $pledge->payments->sum('amount');
                                $outstanding = max(0, $pledge->amount - $collected);
                            @endphp
                            <tr>
                                <td class="px-3 py-3 font-medium text-[var(--color-ink-950)]">{{ $pledge->pledger_name ?: '—' }}</td>
                                <td class="px-3 py-3">{{ ucfirst($pledge->pledge_type ?: '—') }}</td>
                                <td class="px-3 py-3 text-right">{{ number_format($pledge->amount, 2) }}</td>
                                <td class="px-3 py-3 text-right text-emerald-400">{{ number_format($collected, 2) }}</td>
                                <td class="px-3 py-3 text-right {{ $outstanding > 0 ? 'text-rose-400' : 'text-slate-400' }}">{{ number_format($outstanding, 2) }}</td>
                                <td class="px-3 py-3">{{ $pledge->pledge_date ?: '—' }}</td>
                                <td class="px-3 py-3">{{ $pledge->due_date ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-400">No pledges found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

    </section>
</x-layouts.app>
