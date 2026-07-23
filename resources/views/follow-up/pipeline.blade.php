<x-layouts.app title="Follow-Up Pipeline">
    <section class="space-y-8">

        {{-- ── Header ────────────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                    <i class="fa-solid fa-clipboard-list mr-1"></i> Follow-up
                </p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-sitemap mr-2" style="color:#7c3aed;"></i> Pipeline Board
                </h3>
            </div>
            <a href="{{ route('follow-up.tasks') }}" class="btn-secondary">
                <i class="fa-solid fa-list-check mr-1"></i> All Tasks
            </a>
        </div>

        {{-- ── Visitors pipeline (6 stages) ───────────────────────────── --}}
        <div>
            <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">
                <i class="fa-solid fa-person-walking-arrow-right w-4 text-center"></i>
                Visitor Pipeline
            </h4>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6 overflow-x-auto">
                @php
                    $stageColors = [
                        'new'         => ['bg-blue-500',   'bg-blue-50'],
                        'contacted'   => ['bg-amber-500',  'bg-amber-50'],
                        'counseled'   => ['bg-purple-500', 'bg-purple-50'],
                        'joined_zone' => ['bg-teal-500',   'bg-teal-50'],
                        'in_class'    => ['bg-orange-500', 'bg-orange-50'],
                        'converted'   => ['bg-emerald-500','bg-emerald-50'],
                    ];
                    $stageIcons = [
                        'new'         => 'fa-user-plus',
                        'contacted'   => 'fa-phone',
                        'counseled'   => 'fa-user-doctor',
                        'joined_zone' => 'fa-map-pin',
                        'in_class'    => 'fa-book-open',
                        'converted'   => 'fa-check-circle',
                    ];
                @endphp
                @foreach ($stages as $stage => $items)
                    @php [$dot, $colBg] = $stageColors[$stage] ?? ['bg-slate-400', 'bg-slate-50']; @endphp
                    <article class="rounded-2xl border border-[var(--color-surface-200)] p-4 {{ $colBg }}">
                        <div class="mb-3 flex items-center justify-between">
                            <h5 class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-[0.14em] text-slate-600">
                                <i class="fa-solid {{ $stageIcons[$stage] ?? 'fa-circle' }} text-{{ explode('-', $dot)[1] }}-500"></i>
                                {{ str_replace('_', ' ', $stage) }}
                            </h5>
                            <span class="rounded-full {{ $dot }} px-2 py-0.5 text-xs font-bold text-white">
                                {{ $items->count() }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            @forelse ($items as $visitor)
                                <a href="{{ route('visitors.show', $visitor) }}"
                                   class="block rounded-xl border border-white bg-white p-3 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="font-semibold text-[var(--color-ink-950)]">{{ $visitor->full_name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        @if ($visitor->phone)
                                            <i class="fa-solid fa-phone mr-1"></i>{{ $visitor->phone }}
                                        @else
                                            <i class="fa-solid fa-phone-slash mr-1 text-slate-300"></i>No phone
                                        @endif
                                    </p>
                                    @if ($visitor->invited_by)
                                        <p class="mt-0.5 text-xs text-slate-400">
                                            <i class="fa-solid fa-user-group mr-1"></i>{{ $visitor->invited_by }}
                                        </p>
                                    @endif
                                </a>
                            @empty
                                <p class="py-4 text-center text-xs text-slate-400">
                                    <i class="fa-solid fa-inbox mb-1 text-lg block"></i> Empty
                                </p>
                            @endforelse
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- ── Members with follow-up tasks ────────────────────────────── --}}
        @if ($members->count())
        <div>
            <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-emerald-600">
                <i class="fa-solid fa-users w-4 text-center"></i>
                Members With Active Tasks
            </h4>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($members->filter(fn ($m) => $m->followUpTasks?->where('status', '!=', 'completed')->count() > 0) as $member)
                    <a href="{{ route('members.show', $member) }}"
                       class="surface-card block rounded-2xl p-4 hover:bg-[var(--color-surface-50)]">
                        <p class="font-semibold text-[var(--color-ink-950)]">{{ $member->full_name }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            <i class="fa-solid fa-map-pin mr-1"></i>{{ $member->zone ?: '—' }}
                        </p>
                        <p class="mt-1 text-xs text-amber-600">
                            <i class="fa-solid fa-clipboard-list mr-1"></i>
                            {{ $member->followUpTasks->where('status', '!=', 'completed')->count() }} open task(s)
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    </section>
</x-layouts.app>
