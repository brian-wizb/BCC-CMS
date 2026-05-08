<x-layouts.app title="Edit Zone">
    <section class="surface-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Zones</p>
        <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Edit zone</h3>

        <form method="POST" action="{{ route('zones.update', $zone) }}" class="mt-6">
            @method('PUT')
            @include('zones._form', ['submitLabel' => 'Save changes'])
        </form>
    </section>
</x-layouts.app>
