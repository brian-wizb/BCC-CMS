<x-layouts.app title="Events">
    <section class="surface-card p-6">
        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Leadership Operations</p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Events</h3>
            </div>
            <a href="{{ route('events.create') }}" class="btn-primary">Add event</a>
        </div>

        <x-ui.department-zone-filters
            :action="route('events.index')"
            :departments="$departments"
            :zones="$zones"
            :department-id="$departmentId"
            :zone="$zone"
        />

        <div class="mt-6 space-y-3">
            @forelse ($events as $event)
                <a href="{{ route('events.show', $event) }}" class="block rounded-2xl border border-[var(--color-surface-200)] p-4 hover:bg-[var(--color-surface-50)]">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-[var(--color-ink-950)]">{{ $event->title }}</p>
                        <x-ui.status-badge :status="$event->status" />
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ optional($event->start_date)->format('d M Y') }} | {{ $event->location ?: 'No location' }}</p>
                </a>
            @empty
                <p class="text-sm text-slate-400">No events found.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    </section>
</x-layouts.app>
