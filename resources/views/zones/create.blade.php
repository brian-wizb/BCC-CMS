<x-layouts.app title="Add Zone">
    <section class="surface-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Zones</p>
        <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Create zone</h3>

        <form method="POST" action="{{ route('zones.store') }}" class="mt-6">
            @include('zones._form', ['submitLabel' => 'Create zone'])
        </form>
    </section>
</x-layouts.app>
