@php
    $resolveGroupIcon = static function ($icon): string {
        if (! is_string($icon) || trim($icon) === '') {
            return 'fas fa-users';
        }

        return str_contains($icon, 'fas ') ? $icon : 'fas '.trim($icon);
    };
@endphp

<x-layouts.app title="Groups">
    <section class="surface-card p-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.12);">
                    <i class="fas fa-users text-lg" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">People</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Church Groups
                        <span class="ml-1.5 rounded-full px-2 py-0.5 text-xs font-medium" style="background:rgba(99,102,241,0.12); color:rgba(99,102,241,0.9);">{{ $groups->total() }}</span>
                    </h3>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('groups.create')
                <a href="{{ route('groups.create') }}" class="btn-primary flex items-center gap-1.5">
                    <i class="fas fa-plus text-xs"></i> New group
                </a>
                @endcan
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('groups.index') }}" class="mt-5 flex gap-2">
            <div class="relative flex-1 max-w-sm">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input name="search" class="form-input pl-8" placeholder="Search groups..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn-secondary">Search</button>
            @if ($search)
                <a href="{{ route('groups.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>

        {{-- Group grid --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($groups as $group)
                <a href="{{ route('groups.show', $group) }}"
                   class="surface-card block rounded-2xl border border-[var(--color-surface-200)] p-5 hover:border-indigo-300 hover:shadow-md transition group/card overflow-hidden relative">
                    <div class="absolute right-0 top-0 h-24 w-24 rounded-full blur-2xl opacity-20" style="background:{{ $group->color }};"></div>
                    <div class="flex items-start justify-between gap-3">
                        <span class="relative z-10 flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl border text-2xl shadow-sm"
                              style="background:{{ $group->color }}20; color:{{ $group->color }}; border-color:{{ $group->color }}55;">
                            <i class="{{ $resolveGroupIcon($group->icon) }} text-2xl"></i>
                        </span>
                        @if ($group->is_predefined)
                            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Built-in</span>
                        @endif
                    </div>
                    <h4 class="relative z-10 mt-3 font-semibold text-[var(--color-ink-950)] group-hover/card:text-indigo-700 transition text-sm">{{ $group->name }}</h4>
                    <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $group->description ?: 'No description.' }}</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs text-slate-400">
                            <i class="fas fa-user-friends mr-1 opacity-60"></i>{{ number_format($group->memberships_count) }} member{{ $group->memberships_count === 1 ? '' : 's' }}
                        </span>
                        <i class="fas fa-arrow-right text-xs text-indigo-400 opacity-0 group-hover/card:opacity-100 transition"></i>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center">
                    <i class="fas fa-users-slash mb-3 block text-4xl text-slate-300"></i>
                    <p class="text-slate-400">No groups found.</p>
                    @can('groups.create')
                        <a href="{{ route('groups.create') }}" class="btn-primary mt-4 inline-flex items-center gap-1.5">
                            <i class="fas fa-plus text-xs"></i> Create a group
                        </a>
                    @endcan
                </div>
            @endforelse
        </div>

        <div class="mt-5">{{ $groups->links() }}</div>
    </section>
</x-layouts.app>
