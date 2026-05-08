<x-layouts.app title="Add Department">
    <section class="surface-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Departments</p>
        <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Create department</h3>

        <form method="POST" action="{{ route('departments.store') }}" class="mt-6">
            @include('departments._form', ['submitLabel' => 'Create department'])
        </form>
    </section>
</x-layouts.app>
