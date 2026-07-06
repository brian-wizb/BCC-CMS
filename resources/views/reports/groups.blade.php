<x-layouts.app title="Groups Report">
    <section class="space-y-5">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-users" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Groups Report</h3>
                    <p class="text-xs text-slate-500">Registered/guest composition and role distribution by group</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.groups.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.groups')" :date-from="$dateFrom" :date-to="$dateTo" />

        <article class="grid gap-4 sm:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Groups</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ $groups->count() }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Registered</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($groups->sum('registered')) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Guests</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($groups->sum('guests')) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Assignments</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($groups->sum('total')) }}</p>
            </div>
        </article>

        <div class="surface-card p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 pb-3">Group</th>
                            <th class="px-4 pb-3 text-right">Registered</th>
                            <th class="px-4 pb-3 text-right">Guests</th>
                            <th class="px-4 pb-3 text-right">Leaders</th>
                            <th class="px-4 pb-3 text-right">Coordinators</th>
                            <th class="px-4 pb-3 text-right">Members</th>
                            <th class="px-4 pb-3 text-right">Total</th>
                            <th class="px-4 pb-3 w-1/4">Distribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @php $maxTotal = $groups->max('total') ?: 1; @endphp
                        @forelse ($groups as $row)
                            @php
                                $icon = is_string($row['icon']) && str_contains($row['icon'], 'fa-')
                                    ? (str_contains($row['icon'], 'fas ') ? $row['icon'] : 'fas '.$row['icon'])
                                    : 'fas fa-users';
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:{{ $row['color'] }}20;color:{{ $row['color'] }};">
                                            <i class="{{ $icon }} text-xs"></i>
                                        </span>
                                        {{ $row['name'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['registered']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['guests']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['leaders']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['coordinators']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['members']) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ number_format($row['total']) }}</td>
                                <td class="px-4 py-3">
                                    <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                        <div class="h-full rounded-full" style="width:{{ round($row['total'] / $maxTotal * 100) }}%;background:linear-gradient(90deg,rgba(99,102,241,0.8),rgba(52,211,153,0.8));"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No group records found.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($groups->isNotEmpty())
                        <tfoot class="font-semibold text-[var(--color-ink-950)]">
                            <tr class="border-t border-[var(--color-surface-200)]">
                                <td class="px-4 pt-3">Total</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($groups->sum('registered')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($groups->sum('guests')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($groups->sum('leaders')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($groups->sum('coordinators')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($groups->sum('members')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($groups->sum('total')) }}</td>
                                <td class="px-4 pt-3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </section>
</x-layouts.app>
