<x-layouts.app title="Alerts">

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-600 text-white shadow">
                <i class="fa-solid fa-bell text-xl"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Leadership Tools</p>
                <h1 class="text-2xl font-bold text-[var(--color-ink-950)]">Alerts</h1>
                <p class="text-xs text-slate-400 mt-1">Alerts are auto-generated as activities occur.</p>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ────────────────────────────────────────────────────────── --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="surface-card flex items-center gap-4 p-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Open</p>
                <p class="mt-0.5 text-2xl font-bold text-[var(--color-ink-950)]">{{ $openCount }}</p>
            </div>
        </article>
        <article class="surface-card flex items-center gap-4 p-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                <i class="fa-solid fa-eye text-lg"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Acknowledged</p>
                <p class="mt-0.5 text-2xl font-bold text-[var(--color-ink-950)]">{{ $acknowledgedCount }}</p>
            </div>
        </article>
        <article class="surface-card flex items-center gap-4 p-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Critical</p>
                <p class="mt-0.5 text-2xl font-bold text-rose-600">{{ $criticalCount }}</p>
            </div>
        </article>
        <article class="surface-card flex items-center gap-4 p-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-500">
                <i class="fa-solid fa-clock text-lg"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Overdue</p>
                <p class="mt-0.5 text-2xl font-bold text-orange-500">{{ $overdueCount }}</p>
            </div>
        </article>
    </div>

    {{-- ── Filter Bar ────────────────────────────────────────────────────────── --}}
    <section class="surface-card p-5 mb-6">
        <form method="GET" action="{{ route('alerts.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="block mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</label>
                <select name="status" class="form-input w-full">
                    <option value="">All statuses</option>
                    @foreach (['open', 'acknowledged', 'resolved'] as $opt)
                        <option value="{{ $opt }}" @selected($status === $opt)>{{ ucfirst($opt) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wide">Severity</label>
                <select name="severity" class="form-input w-full">
                    <option value="">All severities</option>
                    @foreach (['low', 'medium', 'high', 'critical'] as $opt)
                        <option value="{{ $opt }}" @selected($severity === $opt)>{{ ucfirst($opt) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('alerts.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </section>

    {{-- ── Alert Type Sections ─────────────────────────────────────────────── --}}
    @php
        $typeConfig = [
            'inactive_member'       => ['label' => 'Inactive Members',        'icon' => 'fa-user-slash',          'color' => 'text-slate-700',   'bg' => 'bg-slate-100',   'ring' => 'ring-slate-300'],
            'lapsed_attendance'     => ['label' => 'Lapsed Attendance',        'icon' => 'fa-calendar-xmark',      'color' => 'text-blue-700',    'bg' => 'bg-blue-50',     'ring' => 'ring-blue-200'],
            'pastoral_case_overdue' => ['label' => 'Overdue Pastoral Cases',   'icon' => 'fa-briefcase-medical',   'color' => 'text-orange-700',  'bg' => 'bg-orange-50',   'ring' => 'ring-orange-200'],
            'prayer_request_stale'  => ['label' => 'Stale Prayer Requests',    'icon' => 'fa-hands-praying',       'color' => 'text-violet-700',  'bg' => 'bg-violet-50',   'ring' => 'ring-violet-200'],
            'follow_up_overdue'     => ['label' => 'Follow-up Pending',        'icon' => 'fa-list-check',          'color' => 'text-amber-700',   'bg' => 'bg-amber-50',    'ring' => 'ring-amber-200'],
            'pledge_due'            => ['label' => 'Pledges Due',              'icon' => 'fa-hand-holding-dollar', 'color' => 'text-emerald-700', 'bg' => 'bg-emerald-50',  'ring' => 'ring-emerald-200'],
        ];
        $severityBorder = [
            'low'      => 'border-l-4 border-l-slate-300',
            'medium'   => 'border-l-4 border-l-amber-400',
            'high'     => 'border-l-4 border-l-orange-500',
            'critical' => 'border-l-4 border-l-rose-600',
        ];
        $severityBadge = [
            'low'      => 'bg-slate-100 text-slate-700',
            'medium'   => 'bg-amber-100 text-amber-700',
            'high'     => 'bg-orange-100 text-orange-700',
            'critical' => 'bg-rose-100 text-rose-700',
        ];
    @endphp

    @forelse ($alertsByType as $type => $typeAlerts)
        @php
            $cfg         = $typeConfig[$type] ?? ['label' => ucwords(str_replace('_', ' ', $type)), 'icon' => 'fa-bell', 'color' => 'text-slate-700', 'bg' => 'bg-slate-100', 'ring' => 'ring-slate-300'];
            $criticalCnt = $typeAlerts->where('severity', 'critical')->filter(fn($a) => in_array($a->status, ['open','acknowledged']))->count();
            $overdueCnt  = $typeAlerts->filter(fn($a) => $a->due_at && $a->due_at->isPast() && in_array($a->status, ['open','acknowledged']))->count();
        @endphp

        <details class="mb-4 surface-card overflow-hidden">
            {{-- Section header / summary --}}
            <summary class="flex cursor-pointer select-none list-none items-center justify-between gap-3 border-b border-[var(--color-surface-200)] px-5 py-4 hover:bg-slate-50 transition-colors">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm ring-1 {{ $cfg['bg'] }} {{ $cfg['color'] }} {{ $cfg['ring'] }}">
                        <i class="fa-solid {{ $cfg['icon'] }}"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-[var(--color-ink-950)]">{{ $cfg['label'] }}</p>
                        <p class="text-xs text-slate-400">{{ $typeAlerts->count() }} alert{{ $typeAlerts->count() !== 1 ? 's' : '' }}</p>
                    </div>
                    @if($criticalCnt)
                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-700">
                            <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i>{{ $criticalCnt }} critical
                        </span>
                    @endif
                    @if($overdueCnt)
                        <span class="rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-bold text-orange-700">
                            <i class="fa-solid fa-clock mr-1 text-[10px]"></i>{{ $overdueCnt }} overdue
                        </span>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-down shrink-0 text-xs text-slate-400"></i>
            </summary>

            {{-- Table for this type --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Alert</th>
                            <th class="px-5 py-3">Severity</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Assignee &amp; Due</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @foreach ($typeAlerts as $alert)
                            @php $isOverdue = $alert->due_at && $alert->due_at->isPast() && in_array($alert->status, ['open', 'acknowledged']); @endphp

                            {{-- Data row --}}
                            <tr class="{{ $severityBorder[$alert->severity] ?? '' }} hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-4 max-w-xs">
                                    <p class="font-semibold text-[var(--color-ink-950)] leading-snug">{{ $alert->title }}</p>
                                    @if($alert->status === 'resolved')
                                        {{-- Resolved: title + status badge is enough. No stale message, no redundant badge. --}}
                                    @elseif(isset($conditionDetail[$alert->id]))
                                        @if($conditionActive[$alert->id] ?? true)
                                            {{-- Problem still exists → show message + amber warning badge --}}
                                            <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $alert->message }}</p>
                                            <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700 ring-1 ring-amber-200">
                                                <i class="fa-solid fa-circle-dot text-[9px]"></i> {{ $conditionDetail[$alert->id] }}
                                            </span>
                                        @else
                                            {{-- Condition gone but alert still open/acknowledged → green badge only (cue to close) --}}
                                            <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 ring-1 ring-emerald-200">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> {{ $conditionDetail[$alert->id] }}
                                            </span>
                                        @endif
                                    @else
                                        {{-- No live check available: show stored message --}}
                                        <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $alert->message }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $severityBadge[$alert->severity] ?? 'bg-slate-100 text-slate-700' }}">
                                        @if($alert->severity === 'critical')<i class="fa-solid fa-circle mr-1 text-[8px] align-middle"></i>@endif
                                        {{ ucfirst($alert->severity) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <x-ui.status-badge :status="$alert->status" />
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-tie text-xs text-slate-300"></i>
                                        {{ $alert->assignee?->full_name ?? 'Unassigned' }}
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5 text-xs {{ $isOverdue ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                                        <i class="fa-solid fa-clock text-[10px]"></i>
                                        @if($alert->due_at)
                                            {{ $alert->due_at->format('d M Y') }}
                                            @if($isOverdue)
                                                <span class="ml-1 rounded-full bg-rose-100 px-1.5 py-0.5 text-[10px] font-bold text-rose-700">OVERDUE</span>
                                            @endif
                                        @else
                                            No due date
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <button type="button"
                                            onclick="toggleAlertEdit({{ $alert->id }})"
                                            id="btn-{{ $alert->id }}"
                                            class="btn-secondary inline-flex items-center gap-1.5 text-xs">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </td>
                            </tr>

                            {{-- Expandable edit row --}}
                            <tr id="edit-row-{{ $alert->id }}" class="hidden bg-slate-50 {{ $severityBorder[$alert->severity] ?? '' }} border-b border-[var(--color-surface-200)]">
                                <td colspan="5" class="px-6 py-5">
                                    <div class="rounded-xl border border-[var(--color-surface-200)] bg-white p-5 shadow-sm">
                                        <div class="mb-4 flex items-center justify-between">
                                            <p class="text-sm font-semibold text-[var(--color-ink-950)]">
                                                <i class="fa-solid fa-pen-to-square mr-2 text-[var(--color-brand-600)]"></i>Edit Alert
                                            </p>
                                            <button type="button" onclick="toggleAlertEdit({{ $alert->id }})"
                                                    class="text-slate-400 hover:text-slate-600 transition-colors">
                                                <i class="fa-solid fa-xmark text-base"></i>
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('alerts.update', $alert) }}"
                                              class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-400">Status</label>
                                                <select name="status" class="form-input w-full" required>
                                                    @foreach (['open', 'acknowledged', 'resolved'] as $opt)
                                                        <option value="{{ $opt }}" @selected($alert->status === $opt)>{{ ucfirst($opt) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-400">Severity</label>
                                                <select name="severity" class="form-input w-full" required>
                                                    @foreach (['low', 'medium', 'high', 'critical'] as $opt)
                                                        <option value="{{ $opt }}" @selected($alert->severity === $opt)>{{ ucfirst($opt) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-400">Assign to Leader</label>
                                                <select name="assigned_to" class="form-input w-full">
                                                    <option value="">Unassigned</option>
                                                    @foreach ($leaders as $leader)
                                                        <option value="{{ $leader->id }}" @selected((string) $alert->assigned_to === (string) $leader->id)>
                                                            {{ $leader->full_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-400">Due Date</label>
                                                <input type="datetime-local" name="due_at" class="form-input w-full"
                                                       value="{{ $alert->due_at ? $alert->due_at->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                            <div class="sm:col-span-2 lg:col-span-4 flex flex-wrap gap-2 pt-1">
                                                <button type="submit" class="btn-primary inline-flex items-center gap-2 text-sm">
                                                    <i class="fa-solid fa-check"></i> Save changes
                                                </button>
                                                <button type="button" onclick="toggleAlertEdit({{ $alert->id }})" class="btn-secondary text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                        <form method="POST" action="{{ route('alerts.destroy', $alert) }}"
                                              class="mt-3 pt-3 border-t border-[var(--color-surface-200)]"
                                              data-confirm="Permanently delete this alert?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-100 transition-colors">
                                                <i class="fa-solid fa-trash-can"></i> Delete alert
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>

    @empty
        <div class="surface-card px-5 py-16 text-center">
            <i class="fa-solid fa-bell-slash text-4xl text-slate-200 mb-4 block"></i>
            <p class="text-slate-400">No alerts found for the selected filters.</p>
            @if($status || $severity)
                <a href="{{ route('alerts.index') }}" class="mt-3 inline-block text-sm text-[var(--color-brand-600)] hover:underline">
                    <i class="fa-solid fa-xmark mr-1"></i> Clear filters
                </a>
            @else
                <p class="mt-2 text-xs text-slate-300">Run the alert generator to scan for issues.</p>
            @endif
        </div>
    @endforelse

    @push('scripts')
    <script>
        function toggleAlertEdit(id) {
            const row = document.getElementById('edit-row-' + id);
            const btn = document.getElementById('btn-' + id);
            const isHidden = row.classList.contains('hidden');
            row.classList.toggle('hidden', !isHidden);
            if (btn) {
                btn.innerHTML = isHidden
                    ? '<i class="fa-solid fa-xmark"></i> Close'
                    : '<i class="fa-solid fa-pen-to-square"></i> Edit';
            }
        }
    </script>
    @endpush

</x-layouts.app>
