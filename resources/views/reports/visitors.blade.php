<x-layouts.app title="Visitors Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(167,139,250,0.14);">
                    <i class="fas fa-user-friends" style="color:rgba(167,139,250,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Visitors Report</h3>
                    <p class="text-xs text-slate-500">Visitor flow, status, and conversion to membership</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.visitors.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.visitors')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Visitors</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($total) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Converted to Members</p>
                <p class="mt-1 text-3xl font-bold text-emerald-400">{{ number_format($converted) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Conversion Rate</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ $conversionRate }}%</p>
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                    <div class="h-full rounded-full" style="width:{{ $conversionRate }}%;background:linear-gradient(90deg,rgba(52,211,153,0.8),rgba(36,184,255,0.8));"></div>
                </div>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Male / Female</p>
                @php
                    $males   = $byGender->first(fn ($v, $k) => strtolower($k) === 'male')   ?? 0;
                    $females = $byGender->first(fn ($v, $k) => strtolower($k) === 'female') ?? 0;
                @endphp
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ $males }} / {{ $females }}</p>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- By status --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-tag opacity-60"></i> By Status
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
                        @forelse ($byStatus as $status => $count)
                            <tr>
                                <td class="py-2.5"><x-ui.status-badge :status="$status" /></td>
                                <td class="py-2.5 text-right">{{ number_format($count) }}</td>
                                <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round($count / $total * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">—</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- Monthly trend --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-chart-line opacity-60"></i> Monthly Visitor Trend
                </h4>
                @if ($monthlyVisitors->isEmpty())
                    <p class="text-sm text-slate-400">No data for selected period.</p>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Month</th>
                                <th class="pb-3 text-right">Visitors</th>
                                <th class="pb-3 w-1/3">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @php $maxV = $monthlyVisitors->max('total') ?: 1; @endphp
                            @foreach ($monthlyVisitors as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</td>
                                    <td class="py-2.5 text-right">{{ $row->total }}</td>
                                    <td class="py-2.5">
                                        <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                            <div class="h-full rounded-full" style="width:{{ round($row->total / $maxV * 100) }}%;background:linear-gradient(90deg,rgba(167,139,250,0.8),rgba(36,184,255,0.8));"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </article>
        </div>

        {{-- Recent visitors table --}}
        <article class="surface-card p-6">
            <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                <i class="fas fa-list opacity-60"></i> Visitor List
                <span class="ml-1 text-xs font-normal text-slate-500">(latest 30)</span>
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 pb-3">Name</th>
                            <th class="px-3 pb-3">Gender</th>
                            <th class="px-3 pb-3">Status</th>
                            <th class="px-3 pb-3">First Visit</th>
                            <th class="px-3 pb-3">Invited By</th>
                            <th class="px-3 pb-3 text-center">Converted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($recentVisitors as $visitor)
                            <tr>
                                <td class="px-3 py-3 font-medium text-[var(--color-ink-950)]">{{ $visitor->full_name }}</td>
                                <td class="px-3 py-3">{{ ucfirst($visitor->gender ?: '—') }}</td>
                                <td class="px-3 py-3"><x-ui.status-badge :status="$visitor->status" /></td>
                                <td class="px-3 py-3">{{ optional($visitor->first_visit_date)->format('d M Y') ?: '—' }}</td>
                                <td class="px-3 py-3">{{ $visitor->invited_by ?: '—' }}</td>
                                <td class="px-3 py-3 text-center">
                                    @if ($visitor->converted_member_id)
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400"><i class="fas fa-check text-[10px]"></i></span>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-slate-400">No visitors found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

    </section>
</x-layouts.app>
