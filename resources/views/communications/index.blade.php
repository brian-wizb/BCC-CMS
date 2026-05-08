<x-layouts.app title="Communications">
    <section class="surface-card p-6">
        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Leadership Operations</p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Communications</h3>
            </div>
            <a href="{{ route('communications.create') }}" class="btn-primary">Compose</a>
        </div>

        <div class="mt-6 space-y-3">
            @forelse ($communications as $communication)
                <a href="{{ route('communications.show', $communication) }}" class="block rounded-2xl border border-[var(--color-surface-200)] p-4 hover:bg-[var(--color-surface-50)]">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-[var(--color-ink-950)]">{{ strtoupper($communication->channel) }} to {{ str_replace('_', ' ', $communication->audience_type) }}</p>
                        <x-ui.status-badge :status="$communication->status" />
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $communication->subject ?: 'No subject' }}</p>
                </a>
            @empty
                <p class="text-sm text-slate-400">No communications drafted yet.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $communications->links() }}</div>
    </section>
</x-layouts.app>
