<x-layouts.app title="Event Report">
    <section class="surface-card p-6">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.14);">
                    <i class="fas fa-calendar-alt" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Event Report</h3>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.events.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <a href="{{ route('reports.index') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs"></i> All reports
                </a>
            </div>
        </div>

        <x-ui.department-zone-filters
            :action="route('reports.events')"
            :departments="$departments"
            :zones="$zones"
            :department-id="$departmentId"
            :zone="$zone"
        />

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3"><i class="fas fa-calendar-alt mr-1.5 opacity-60"></i>Event</th>
                        <th class="px-4 py-3"><i class="fas fa-clock mr-1.5 opacity-60"></i>Date</th>
                        <th class="px-4 py-3"><i class="fas fa-info-circle mr-1.5 opacity-60"></i>Status</th>
                        <th class="px-4 py-3"><i class="fas fa-user-plus mr-1.5 opacity-60"></i>Registrations</th>
                        <th class="px-4 py-3"><i class="fas fa-hands-helping mr-1.5 opacity-60"></i>Volunteer Assignments</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($events as $event)
                        <tr>
                            <td class="px-4 py-4 font-medium text-[var(--color-ink-950)]">{{ $event->title }}</td>
                            <td class="px-4 py-4">{{ optional($event->start_date)->format('d M Y') ?: '—' }}</td>
                            <td class="px-4 py-4"><x-ui.status-badge :status="$event->status" /></td>
                            <td class="px-4 py-4">{{ $event->registrations_count }}</td>
                            <td class="px-4 py-4">{{ $event->volunteer_assignments_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No event records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    </section>
</x-layouts.app>
