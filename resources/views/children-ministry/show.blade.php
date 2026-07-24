<x-layouts.app title="Child Details">
    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <article class="surface-card p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('children-ministry.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-[var(--color-surface-100)] transition" title="Back to children">
                        <i class="fas fa-arrow-left text-slate-500 text-sm"></i>
                    </a>
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] shrink-0">
                        <i class="fas fa-child text-2xl text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Children Ministry</p>
                        <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $child->full_name }}</h3>
                    </div>
                </div>
                <a href="{{ route('children-ministry.edit', $child) }}" class="btn-secondary shrink-0">Edit</a>
            </div>

            <dl class="mt-6 grid gap-4 md:grid-cols-2">
                {{-- First Name --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-user text-sm opacity-50"></i>First name
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $child->first_name ?: '—' }}</dd>
                </div>

                {{-- Middle Name --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-user text-sm opacity-50"></i>Middle name
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $child->middle_name ?: '—' }}</dd>
                </div>

                {{-- Surname --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-user text-sm opacity-50"></i>Surname
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $child->surname ?: '—' }}</dd>
                </div>

                {{-- Sex --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-venus-mars text-sm opacity-50"></i>Sex
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $child->sex ?: '—' }}</dd>
                </div>

                {{-- Date of Birth --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-calendar text-sm opacity-50"></i>Date of birth
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ optional($child->date_of_birth)->format('d M Y') ?: '—' }}</dd>
                </div>

                {{-- Parent Name --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-user-tie text-sm opacity-50"></i>Parent name
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $child->parent_name ?: '—' }}</dd>
                </div>

                {{-- Parent Contact --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-phone text-sm opacity-50"></i>Parent contact
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $child->parent_contact ?: '—' }}</dd>
                </div>

                {{-- Linked Member --}}
                <div class="rounded-2xl border @if($child->parentMember) border-blue-200 bg-blue-50/40 @else border-[var(--color-surface-200)] @endif p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] @if($child->parentMember) text-blue-500 @else text-slate-400 @endif">
                        <i class="fas fa-link text-sm opacity-60"></i>Linked member
                    </dt>
                    <dd class="mt-2 text-sm font-medium">
                        @if ($child->parentMember)
                            <a href="{{ route('members.show', $child->parentMember) }}" class="text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center gap-1.5">
                                {{ $child->parentMember->full_name }}
                                <i class="fas fa-external-link-alt text-xs opacity-60"></i>
                            </a>
                        @else
                            <span class="text-slate-600">—</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </article>

        {{-- Right column --}}
        <div class="flex flex-col gap-6">
            <article class="surface-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Actions</p>
                <div class="mt-3 space-y-2">
                    <a href="{{ route('children-ministry.edit', $child) }}" class="btn-secondary w-full flex items-center justify-center gap-1.5">
                        <i class="fas fa-pen text-xs"></i> Edit
                    </a>
                    @can('children_ministry.delete')
                        <form method="POST" action="{{ route('children-ministry.destroy', $child) }}" data-confirm="Delete {{ $child->full_name }}?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary w-full flex items-center justify-center gap-1.5 text-red-600 hover:bg-red-50">
                                <i class="fas fa-trash text-xs"></i> Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </article>

            <article class="surface-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Remarks</p>
                <div class="mt-3 rounded-2xl bg-[var(--color-surface-50)] p-4">
                    <p class="text-sm leading-6 text-slate-600">{{ $child->remarks ?: 'No remarks recorded.' }}</p>
                </div>
            </article>

            <article class="surface-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Record Info</p>
                <div class="mt-3 space-y-4 text-sm text-slate-600">
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Date added</p>
                        <p>{{ $child->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Last updated</p>
                        <p>{{ $child->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </article>
        </div>
    </section>
</x-layouts.app>
