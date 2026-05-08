<x-layouts.app title="Edit Visitor">
    <section class="surface-card p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                    <i class="fa-solid fa-users mr-1"></i> Visitors
                </p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-user-pen mr-2" style="color:#2563eb;"></i> Edit Visitor
                </h3>
                <p class="mt-1 text-sm text-slate-500">{{ $visitor->full_name }}</p>
            </div>
            <a href="{{ route('visitors.show', $visitor) }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to visitor
            </a>
        </div>
        <form method="POST" action="{{ route('visitors.update', $visitor) }}">
            @method('PUT')
            @include('visitors._form', ['submitLabel' => 'Save changes'])
        </form>
    </section>
</x-layouts.app>
