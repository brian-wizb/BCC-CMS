<x-layouts.app title="Communications Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-paper-plane" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Communications Report</h3>
                    <p class="text-xs text-slate-500">Message volume, delivery rates, and channel breakdown</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.communications.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.communications')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Messages</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($total) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Sent</p>
                <p class="mt-1 text-2xl font-bold text-emerald-400">{{ number_format($sent) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Pending</p>
                <p class="mt-1 text-2xl font-bold" style="color:rgba(244,193,93,0.9);">{{ number_format($pending) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Failed</p>
                <p class="mt-1 text-2xl font-bold text-rose-400">{{ number_format($failed) }}</p>
            </div>
        </article>

        {{-- Delivery rate highlight --}}
        <article class="surface-card p-5 flex flex-wrap items-center gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Delivery Rate</p>
                <p class="mt-1 text-3xl font-bold" style="color:rgba(99,102,241,0.9);">{{ $deliveryRate }}%</p>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($deliveredCount) }} delivered of {{ number_format($totalDeliveries) }} tracked deliveries</p>
            </div>
            <div class="h-2 flex-1 overflow-hidden rounded-full bg-[var(--color-surface-200)] min-w-[120px]">
                <div class="h-full rounded-full" style="width:{{ $deliveryRate }}%;background:linear-gradient(90deg,rgba(99,102,241,0.7),rgba(167,139,250,0.7));"></div>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- By Channel --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-broadcast-tower opacity-60"></i> By Channel
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Channel</th>
                            <th class="pb-3 text-right">Messages</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byChannel as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucfirst($row->channel) }}</td>
                                <td class="py-2.5 text-right">{{ $row->total }}</td>
                                <td class="py-2.5 text-right text-slate-500">
                                    {{ $total > 0 ? round($row->total / $total * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">No channel data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- By Audience Type --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-users opacity-60"></i> By Audience Type
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Audience</th>
                            <th class="pb-3 text-right">Messages</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byAudienceType as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucwords(str_replace('_', ' ', $row->audience_type)) }}</td>
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

            {{-- Monthly Trend --}}
            <article class="surface-card p-6 lg:col-span-2">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-chart-line opacity-60"></i> Monthly Trend
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Month</th>
                                <th class="pb-3 text-right">Messages Sent</th>
                                <th class="pb-3 w-1/3">Volume</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @php $maxTrend = $monthlyTrend->max('total') ?: 1; @endphp
                            @forelse ($monthlyTrend as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->month }}</td>
                                    <td class="py-2.5 text-right">{{ number_format($row->total) }}</td>
                                    <td class="py-2.5">
                                        <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                            <div class="h-full rounded-full" style="width:{{ round($row->total / $maxTrend * 100) }}%;background:linear-gradient(90deg,rgba(99,102,241,0.7),rgba(167,139,250,0.7));"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-center text-slate-400">No trend data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>

        {{-- Recent Communications --}}
        <article class="surface-card p-6">
            <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                <i class="fas fa-history opacity-60"></i> Recent Communications
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 pb-3">Subject</th>
                            <th class="px-3 pb-3">Channel</th>
                            <th class="px-3 pb-3">Audience</th>
                            <th class="px-3 pb-3">Status</th>
                            <th class="px-3 pb-3">Created By</th>
                            <th class="px-3 pb-3 text-right">Deliveries</th>
                            <th class="px-3 pb-3 text-right">Delivered</th>
                            <th class="px-3 pb-3">Sent At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($recentComms as $comm)
                            <tr>
                                <td class="px-3 py-3 font-medium text-[var(--color-ink-950)] max-w-[200px] truncate">{{ $comm->subject }}</td>
                                <td class="px-3 py-3">{{ ucfirst($comm->channel) }}</td>
                                <td class="px-3 py-3 text-slate-400">{{ ucwords(str_replace('_', ' ', $comm->audience_type)) }}</td>
                                <td class="px-3 py-3"><x-ui.status-badge :status="$comm->status" /></td>
                                <td class="px-3 py-3 text-slate-400">{{ $comm->creator?->full_name ?? '—' }}</td>
                                <td class="px-3 py-3 text-right">{{ $comm->deliveries_count }}</td>
                                <td class="px-3 py-3 text-right text-emerald-400">{{ $comm->delivered_count }}</td>
                                <td class="px-3 py-3 text-slate-400">{{ optional($comm->sent_at)->format('d M Y') ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-3 py-8 text-center text-slate-400">No communications found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

    </section>
</x-layouts.app>
