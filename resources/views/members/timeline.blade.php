<x-layouts.app :title="'Timeline - '.$member->full_name">
    <section class="surface-card p-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Member History</p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $member->full_name }} timeline</h3>
                <p class="mt-2 text-sm text-slate-500">Unified events across membership, attendance, prayer, pastoral care, and custom timeline records.</p>
            </div>
            <a href="{{ route('members.show', $member) }}" class="btn-secondary">Back to member</a>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($events as $event)
                <article class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <p class="font-semibold text-[var(--color-ink-950)]">{{ $event['title'] }}</p>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ str_replace('_', ' ', $event['event_type']) }} · {{ $event['event_date']->format('d M Y H:i') }}
                        </p>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $event['details'] ?: 'No details provided.' }}</p>
                </article>
            @empty
                <p class="rounded-2xl border border-dashed border-[var(--color-surface-200)] p-8 text-center text-sm text-slate-500">
                    No timeline events found for this member.
                </p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
