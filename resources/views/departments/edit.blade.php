<x-layouts.app title="Edit Department">
    <section class="surface-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Departments</p>
        <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Edit department</h3>

        <form method="POST" action="{{ route('departments.update', $department) }}" class="mt-6">
            @method('PUT')
            @include('departments._form', ['submitLabel' => 'Save changes'])
        </form>
    </section>
</x-layouts.app>
