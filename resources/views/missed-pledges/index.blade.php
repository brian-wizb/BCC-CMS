<x-layouts.app title="Missed Pledges">
    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(239,68,68,0.12);">
                    <i class="fas fa-calendar-times text-base" style="color:rgba(239,68,68,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Missed Pledges</h3>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('missed-pledges.export', request()->only(['search', 'date_from', 'date_to'])) }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <button type="button" onclick="window.print()" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-print text-xs"></i> Print
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('missed-pledges.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative min-w-[180px] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input name="search" class="form-input w-full pl-8" value="{{ $search ?? '' }}" placeholder="Name, phone or campaign...">
            </div>
            <button type="submit" class="btn-secondary">Search</button>
            @if(!empty($search))
                <a href="{{ route('missed-pledges.index') }}" class="btn-secondary flex items-center gap-1"><i class="fas fa-times text-xs"></i></a>
            @endif
        </form>

        <x-ui.date-range-filters :action="route('missed-pledges.index')" :date-from="$dateFrom" :date-to="$dateTo" />

        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-user mr-1.5 opacity-60"></i>Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-phone mr-1.5 opacity-60"></i>Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-bullhorn mr-1.5 opacity-60"></i>Campaign</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-coins mr-1.5 opacity-60"></i>Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-check-circle mr-1.5 opacity-60"></i>Paid Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-exclamation-circle mr-1.5 opacity-60"></i>Due Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-calendar-alt mr-1.5 opacity-60"></i>Start Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-calendar-day mr-1.5 opacity-60"></i>Due/End Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($missedPledges as $i => $pledge)
                        @php $paid = $pledge->payments->sum('amount'); $due = max(0, $pledge->amount - $paid); @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">
                                <a href="{{ route('pledges.edit', $pledge) }}" class="hover:text-blue-600">{{ $pledge->pledger_name ?: '—' }}</a>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $pledge->pledger_phone ?: '—' }}</td>
                            <td class="px-4 py-3">
                                @if($pledge->campaign)
                                    <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold" style="background:rgba(245,158,11,0.12); color:rgba(245,158,11,0.9);">{{ $pledge->campaign->name }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ number_format($pledge->amount, 2) }}</td>
                            <td class="px-4 py-3 text-emerald-700 font-medium">{{ number_format($paid, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-rose-600">{{ number_format($due, 2) }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3 text-rose-500 font-medium">{{ \Carbon\Carbon::parse($pledge->due_date)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <i class="fas fa-calendar-check mb-3 block text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-400">No overdue pledges — all pledges are fully paid or not yet due.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</x-layouts.app>
