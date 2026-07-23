<x-layouts.app title="Edit Member">
    <section class="surface-card p-6">

        <div class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.12);">
                    <i class="fas fa-user-edit text-base" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Members</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit &mdash; {{ $member->full_name }}</h3>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('members.show', $member) }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                    <i class="fas fa-eye text-xs"></i> View profile
                </a>
                <a href="{{ route('members.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                    <i class="fas fa-arrow-left text-xs"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('members._form', ['submitLabel' => 'Update member'])
        </form>
    </section>
</x-layouts.app>
