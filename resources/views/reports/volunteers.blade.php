<x-layouts.app title="Volunteer Report">
    <section class="space-y-6">
        <article class="grid gap-4 md:grid-cols-5">
            <div class="surface-card p-4 flex items-center gap-3">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl" style="background:rgba(78,121,167,0.16);">
                    <i class="fas fa-id-card" style="color:#4e79a7;"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $summary['total'] }}</p>
                </div>
            </div>
            <div class="surface-card p-4 flex items-center gap-3">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl" style="background:rgba(242,142,43,0.16);">
                    <i class="fas fa-tasks" style="color:#f28e2b;"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Assigned</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $summary['assigned'] }}</p>
                </div>
            </div>
            <div class="surface-card p-4 flex items-center gap-3">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl" style="background:rgba(118,183,178,0.16);">
                    <i class="fas fa-check-double" style="color:#76b7b2;"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Confirmed</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $summary['confirmed'] }}</p>
                </div>
            </div>
            <div class="surface-card p-4 flex items-center gap-3">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl" style="background:rgba(89,161,79,0.16);">
                    <i class="fas fa-check-circle" style="color:#59a14f;"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Completed</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $summary['completed'] }}</p>
                </div>
            </div>
            <div class="surface-card p-4 flex items-center gap-3">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl" style="background:rgba(225,87,89,0.16);">
                    <i class="fas fa-times-circle" style="color:#e15759;"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Cancelled</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $summary['cancelled'] }}</p>
                </div>
            </div>
        </article>

        <article class="surface-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(255,111,145,0.14);">
                        <i class="fas fa-hands-helping" style="color:rgba(255,111,145,0.9);"></i>
                    </span>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Volunteer Assignment Report</h3>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.volunteers.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
                        <i class="fas fa-download text-xs"></i> Export CSV
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn-secondary flex items-center gap-1.5">
                        <i class="fas fa-arrow-left text-xs"></i> All reports
                    </a>
                </div>
            </div>

            <x-ui.report-filters
                :action="route('reports.volunteers')"
                :departments="$departments"
                :zones="$zones"
                :department-id="$departmentId"
                :zone="$zone"
                :date-from="$dateFrom"
                :date-to="$dateTo"
            />

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3"><i class="fas fa-user mr-1.5 opacity-60"></i>Volunteer</th>
                            <th class="px-4 py-3"><i class="fas fa-tag mr-1.5 opacity-60"></i>Role</th>
                            <th class="px-4 py-3"><i class="fas fa-calendar-alt mr-1.5 opacity-60"></i>Event</th>
                            <th class="px-4 py-3"><i class="fas fa-sitemap mr-1.5 opacity-60"></i>Department</th>
                            <th class="px-4 py-3"><i class="fas fa-info-circle mr-1.5 opacity-60"></i>Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td class="px-4 py-4 font-medium text-[var(--color-ink-950)]">{{ $assignment->member?->full_name ?: '—' }}</td>
                                <td class="px-4 py-4">{{ $assignment->role }}</td>
                                <td class="px-4 py-4">{{ $assignment->event?->title ?: '—' }}</td>
                                <td class="px-4 py-4">{{ $assignment->department?->name ?: '—' }}</td>
                                <td class="px-4 py-4"><x-ui.status-badge :status="$assignment->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No volunteer assignments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $assignments->links() }}</div>
        </article>
    </section>
</x-layouts.app>
