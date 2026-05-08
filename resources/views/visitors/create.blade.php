<x-layouts.app title="Add Visitor">
    <section class="surface-card p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                    <i class="fa-solid fa-users mr-1"></i> Visitors
                </p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-user-plus mr-2" style="color:#2563eb;"></i> Capture Visitor
                </h3>
            </div>
            <a href="{{ route('visitors.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to list
            </a>
        </div>
        <form method="POST" action="{{ route('visitors.store') }}">
            @include('visitors._form', ['submitLabel' => 'Save visitor'])
        </form>
    </section>
</x-layouts.app>
