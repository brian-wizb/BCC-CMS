<x-layouts.app title="Department Report">
    <section class="space-y-5">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-sitemap" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Department Report</h3>
                    <p class="text-xs text-slate-500">Membership, attendance, and assignments by department</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.departments.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.departments')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Summary stat cards --}}
        <article class="grid gap-4 sm:grid-cols-3">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Departments</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ $departments->count() }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Attendance Records</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($departments->sum('attendance')) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Completed Assignments</p>
                <p class="mt-1 text-3xl font-bold text-[var(--color-ink-950)]">{{ number_format($departments->sum('completed_assignments')) }}</p>
            </div>
        </article>

        <div class="surface-card p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 pb-3"><i class="fas fa-sitemap mr-1.5 opacity-60"></i>Department</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-users mr-1.5 opacity-60"></i>Members</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-calendar-check mr-1.5 opacity-60"></i>Attendance</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-percentage mr-1.5 opacity-60"></i>Att. Rate</th>
                            <th class="px-4 pb-3 text-right"><i class="fas fa-tasks mr-1.5 opacity-60"></i>Completed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($departments as $dept)
                            <tr>
                                <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $dept['name'] }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($dept['members']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($dept['attendance']) }}</td>
                                <td class="px-4 py-3 text-right">{{ $dept['attendance_rate'] }}x</td>
                                <td class="px-4 py-3 text-right">{{ number_format($dept['completed_assignments']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No department records found.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($departments->isNotEmpty())
                        <tfoot class="font-semibold text-[var(--color-ink-950)]">
                            <tr class="border-t border-[var(--color-surface-200)]">
                                <td class="px-4 pt-3">Total</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($departments->sum('members')) }}</td>
                                <td class="px-4 pt-3 text-right">{{ number_format($departments->sum('attendance')) }}</td>
                                <td class="px-4 pt-3"></td>
                                <td class="px-4 pt-3 text-right">{{ number_format($departments->sum('completed_assignments')) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </section>
</x-layouts.app>
