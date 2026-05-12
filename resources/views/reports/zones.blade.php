<x-layouts.app title="Zone Report">
    <section class="space-y-5">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-map-marked-alt" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Zone Report</h3>
                    <p class="text-xs text-slate-500">Member count, attendance, and engagement by zone</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.zones.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.zones')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Summary stat cards --}}
        <article class="grid gap-4 sm:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Zones</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ $zones->count() }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Members</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($zones->sum('members')) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Families</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($zones->sum('families')) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Attendance Records</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($zones->sum('attendance')) }}</p>
            </div>
        </article>

        <div class="surface-card p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 pb-3"><i class="fas fa-map-marker-alt mr-1.5 opacity-60"></i>Zone</th>
                            <th class="px-4 pb-3"><i class="fas fa-user-tie mr-1.5 opacity-60"></i>Leader</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-users mr-1.5 opacity-60"></i>Members</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-home mr-1.5 opacity-60"></i>Families</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-calendar-check mr-1.5 opacity-60"></i>Attendance</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-percentage mr-1.5 opacity-60"></i>Att. Rate</th>
                            <th class="px-4 pb-3 w-1/4">Distribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @php $maxAtt = $zones->max('attendance') ?: 1; @endphp
                        @forelse ($zones as $zone)
                            <tr>
                                <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $zone['name'] }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $zone['leader'] }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($zone['members']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($zone['families']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($zone['attendance']) }}</td>
                                <td class="px-4 py-3 text-right">{{ $zone['attendance_rate'] }}x</td>
                                <td class="px-4 py-3">
                                    <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                        <div class="h-full rounded-full" style="width:{{ round($zone['attendance'] / $maxAtt * 100) }}%;background:linear-gradient(90deg,rgba(52,211,153,0.7),rgba(36,184,255,0.7));"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No zone records found.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($zones->isNotEmpty())
                        <tfoot class="font-semibold text-[var(--color-ink-950)]">
                            <tr class="border-t border-[var(--color-surface-200)]">
                                <td class="px-4 pt-3">Total</td>
                                <td class="px-4 pt-3"></td>
                                <td class="px-4 pt-3 text-right">{{ number_format($zones->sum('members')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($zones->sum('families')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($zones->sum('attendance')) }}</td>
                                <td colspan="2" class="px-4 pt-3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </section>
</x-layouts.app>
