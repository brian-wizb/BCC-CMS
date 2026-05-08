<x-layouts.app title="Family Profile">

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">

        {{-- LEFT: details + linked members --}}
        <div class="space-y-5">

            <article class="surface-card p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl text-lg font-bold"
                              style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">
                            {{ mb_strtoupper(mb_substr($family->head_of_family, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Family record</p>
                            <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">{{ $family->head_of_family }}</h3>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('families.edit', $family) }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                            <i class="fas fa-pen text-xs"></i> Edit
                        </a>
                        <a href="{{ route('families.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                            <i class="fas fa-arrow-left text-xs"></i> Back
                        </a>
                    </div>
                </div>

                <dl class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @php
                        $details = [
                            ['fa-venus-mars',     'Gender',     $family->gender],
                            ['fa-phone',          'Phone',      $family->phone],
                            ['fa-map-marker-alt', 'Zone',       $family->zone ?? null],
                            ['fa-users',          'Members',    $family->members->count()],
                            ['fa-map-pin',        'Address',    $family->address ?? null],
                            ['fa-user-friends',   'Cell group', $family->home_cell_group ?? null],
                            ['fa-calendar-plus',  'Joined',     optional($family->joined_date)->format('d M Y')],
                            ['fa-clock',          'Recorded',   $family->created_at?->format('d M Y')],
                        ];
                    @endphp
                    @foreach ($details as [$icon, $label, $value])
                        <div class="rounded-xl border border-[var(--color-surface-200)] px-4 py-3">
                            <dt class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                                <i class="fas {{ $icon }} opacity-60"></i> {{ $label }}
                            </dt>
                            <dd class="mt-1.5 text-sm text-[var(--color-ink-950)]">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if (!empty($family->remarks))
                    <div class="mt-4 rounded-xl bg-[var(--color-surface-50)] px-4 py-3">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                            <i class="fas fa-comment-alt mr-1 opacity-60"></i> Notes
                        </p>
                        <p class="text-sm leading-6 text-slate-600">{{ $family->remarks }}</p>
                    </div>
                @endif
            </article>

            {{-- Linked members --}}
            <article class="surface-card overflow-hidden">
                <div class="flex items-center gap-2 border-b border-[var(--color-surface-200)] px-6 py-4">
                    <i class="fas fa-users text-sm" style="color:rgba(52,211,153,0.9);"></i>
                    <h4 class="font-semibold text-[var(--color-ink-950)]">Family members</h4>
                    <span class="ml-0.5 rounded-full px-1.5 py-0.5 text-xs font-semibold"
                          style="background:rgba(52,211,153,0.12); color:rgba(52,211,153,0.9);">{{ $family->members->count() }}</span>
                </div>

                @if ($family->members->isEmpty())
                    <div class="px-6 py-8 text-center">
                        <i class="fas fa-user-slash mb-2 block text-2xl text-slate-300"></i>
                        <p class="text-sm text-slate-400">No members linked to this family yet.</p>
                        <p class="mt-1 text-xs text-slate-400">Open a member profile and set their family to link them here.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                        <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">
                            <tr>
                                <th class="px-6 py-3"><i class="fas fa-user mr-1.5 opacity-60"></i>Name</th>
                                <th class="px-6 py-3"><i class="fas fa-venus-mars mr-1.5 opacity-60"></i>Gender</th>
                                <th class="px-6 py-3"><i class="fas fa-phone mr-1.5 opacity-60"></i>Phone</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @foreach ($family->members as $member)
                                <tr class="transition hover:bg-[var(--color-surface-50)]">
                                    <td class="px-6 py-3 font-medium text-[var(--color-ink-950)]">
                                        <span class="flex items-center gap-2">
                                            <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                                  style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">
                                                {{ mb_strtoupper(mb_substr($member->full_name ?? $member->first_name ?? '?', 0, 1)) }}
                                            </span>
                                            {{ $member->full_name ?? ($member->first_name . ' ' . $member->last_name) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-slate-500">{{ $member->gender ?: '—' }}</td>
                                    <td class="px-6 py-3 text-slate-500">{{ $member->phone ?: '—' }}</td>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="btn-secondary flex w-fit items-center gap-1 px-2.5 py-1 text-xs">
                                            <i class="fas fa-eye text-[10px]"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </article>

        </div>

        {{-- RIGHT: pastoral cases + attendance --}}
        <div class="space-y-5">

            <article class="surface-card p-5">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-hands-helping text-sm" style="color:rgba(167,139,250,0.9);"></i>
                    <h4 class="font-semibold text-[var(--color-ink-950)]">Pastoral care</h4>
                    <span class="rounded-full px-1.5 py-0.5 text-xs font-semibold"
                          style="background:rgba(167,139,250,0.12); color:rgba(167,139,250,0.9);">{{ $family->pastoralCases->count() }}</span>
                </div>
                @forelse ($family->pastoralCases->take(5) as $case)
                    <div class="mb-3 rounded-xl border border-[var(--color-surface-200)] p-3 text-sm">
                        <p class="font-medium text-[var(--color-ink-950)]">{{ $case->subject ?? $case->category ?? 'Case #'.$case->id }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">
                            {{ optional($case->opened_at)->format('d M Y') }}
                            @if (!empty($case->assignee))
                                &mdash; {{ $case->assignee->name }}
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No pastoral cases on record.</p>
                @endforelse
            </article>

            <article class="surface-card p-5">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-sm" style="color:rgba(36,184,255,0.9);"></i>
                    <h4 class="font-semibold text-[var(--color-ink-950)]">Recent attendance</h4>
                    <span class="rounded-full px-1.5 py-0.5 text-xs font-semibold"
                          style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">{{ $family->attendanceRecords->count() }}</span>
                </div>
                @forelse ($family->attendanceRecords->take(5) as $record)
                    <div class="mb-2 flex items-center justify-between text-sm">
                        <span class="text-[var(--color-ink-950)]">{{ optional($record->recorded_at)->format('d M Y') }}</span>
                        <span class="text-xs text-slate-400">{{ $record->status ?? '' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No attendance records yet.</p>
                @endforelse
            </article>

        </div>
    </section>
</x-layouts.app>
