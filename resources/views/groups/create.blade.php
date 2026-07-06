<x-layouts.app title="New Group">
    <section class="mx-auto max-w-xl">
        <div class="mb-5 flex items-center gap-3">
            <a href="{{ route('groups.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg hover:bg-[var(--color-surface-100)] transition">
                <i class="fas fa-arrow-left text-slate-500 text-sm"></i>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Groups</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Create new group</h3>
            </div>
        </div>

        <article class="surface-card p-6">
            <form method="POST" action="{{ route('groups.store') }}">
                @csrf
                @include('groups._form')
            </form>
        </article>
    </section>
</x-layouts.app>
