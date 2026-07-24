<x-layouts.app title="Add Child">
    <section class="surface-card p-6">

        <div class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.12);">
                    <i class="fas fa-child text-base" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Children Ministry</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Add new child</h3>
                </div>
            </div>
            <a href="{{ route('children-ministry.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back to children
            </a>
        </div>

        <form method="POST" action="{{ route('children-ministry.store') }}" enctype="multipart/form-data">
            @csrf
            @include('children-ministry._form', ['submitLabel' => 'Add child', 'child' => null, 'members' => $members])
        </form>
    </section>
</x-layouts.app>
