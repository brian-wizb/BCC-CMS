<x-layouts.app title="Follow-Up Tasks">
    <section class="space-y-6">
        @php
            $canCreate = auth()->user()->hasPermission('follow_up.create');
            $canUpdate = auth()->user()->hasPermission('follow_up.update');
            $canDelete = auth()->user()->hasPermission('follow_up.delete');
        @endphp

        {{-- ── Page header ───────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                    <i class="fa-solid fa-clipboard-list mr-1"></i> Follow-up
                </p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-list-check mr-2" style="color:#16a34a;"></i> Tasks
                </h3>
            </div>
            <a href="{{ route('follow-up.pipeline') }}" class="btn-secondary">
                <i class="fa-solid fa-sitemap mr-1"></i> View Pipeline
            </a>
        </div>

        @if ($canCreate)
            {{-- ── Create task form ───────────────────────────────────────── --}}
            <article class="surface-card p-6">
                <h4 class="mb-5 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">
                    <i class="fa-solid fa-plus-circle w-4 text-center"></i> New Follow-up Task
                </h4>
                <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-3" method="POST"
                      action="{{ route('follow-up.tasks.store') }}" id="task-create-form">
                    @csrf

                {{-- Person type selector --}}
                <div>
                    <label class="form-label" for="person_type">Person type <span class="text-red-500">*</span></label>
                    <select id="person_type" name="person_type" class="form-input" required
                            onchange="switchPersonList(this.value)">
                        <option value="visitor">Visitor</option>
                        <option value="member">Member</option>
                        <option value="family">Family</option>
                    </select>
                </div>

                {{-- Dynamic person dropdown --}}
                <div>
                    <label class="form-label">Person <span class="text-red-500">*</span></label>

                    <select id="person_id_visitor" name="person_id" class="form-input" required>
                        <option value="">— select visitor —</option>
                        @foreach ($visitors as $v)
                            <option value="{{ $v->id }}">{{ $v->full_name }}</option>
                        @endforeach
                    </select>

                    <select id="person_id_member" name="person_id" class="form-input hidden" disabled>
                        <option value="">— select member —</option>
                        @foreach ($members as $m)
                            <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                        @endforeach
                    </select>

                    <select id="person_id_family" name="person_id" class="form-input hidden" disabled>
                        <option value="">— select family —</option>
                        @foreach ($families as $f)
                            <option value="{{ $f->id }}">{{ $f->head_of_family }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Task type --}}
                <div>
                    <label class="form-label" for="c_task_type">Task type <span class="text-red-500">*</span></label>
                    <select id="c_task_type" name="task_type" class="form-input" required>
                        @foreach (['call' => 'Call', 'sms' => 'SMS', 'visit' => 'Visit', 'prayer' => 'Prayer', 'counseling' => 'Counseling', 'zone_assignment' => 'Zone Assignment'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Priority --}}
                <div>
                    <label class="form-label" for="c_priority">Priority <span class="text-red-500">*</span></label>
                    <select id="c_priority" name="priority" class="form-input" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                {{-- Assign leader --}}
                <div>
                    <label class="form-label" for="c_leader_id">Assigned leader</label>
                    <select id="c_leader_id" name="leader_id" class="form-input">
                        <option value="">— unassigned —</option>
                        @foreach ($leaders as $leader)
                            <option value="{{ $leader->id }}">{{ $leader->full_name }} ({{ $leader->role }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Due date --}}
                <div>
                    <label class="form-label" for="c_due_date">Due date</label>
                    <input id="c_due_date" type="date" name="due_date" class="form-input">
                </div>

                {{-- Status --}}
                <div>
                    <label class="form-label" for="c_status">Status <span class="text-red-500">*</span></label>
                    <select id="c_status" name="status" class="form-input" required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2 xl:col-span-2">
                    <label class="form-label" for="c_notes">Notes</label>
                    <input id="c_notes" name="notes" class="form-input" placeholder="Optional task notes">
                </div>

                <div class="flex items-end md:col-span-2 xl:col-span-3">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-plus mr-1"></i> Create task
                    </button>
                </div>
            </form>
        </article>
        @endif

        {{-- ── Filter bar ─────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('follow-up.tasks') }}"
              class="flex flex-wrap gap-3">
            <select name="status" class="form-input max-w-[170px]">
                <option value="">All statuses</option>
                @foreach (['pending' => 'Pending', 'in_progress' => 'In progress', 'completed' => 'Completed'] as $val => $label)
                    <option value="{{ $val }}" @selected($status === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="priority" class="form-input max-w-[150px]">
                <option value="">All priorities</option>
                @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $val => $label)
                    <option value="{{ $val }}" @selected($priority === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="type" class="form-input max-w-[180px]">
                <option value="">All types</option>
                @foreach (['call' => 'Call', 'sms' => 'SMS', 'visit' => 'Visit', 'prayer' => 'Prayer', 'counseling' => 'Counseling', 'zone_assignment' => 'Zone Assignment'] as $val => $label)
                    <option value="{{ $val }}" @selected($type === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn-secondary" type="submit">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('follow-up.tasks') }}" class="btn-secondary">
                <i class="fa-solid fa-xmark mr-1"></i> Clear
            </a>
        </form>

        {{-- ── Task cards ─────────────────────────────────────────────── --}}
        <article class="surface-card p-6">
            <div class="space-y-4">
                @forelse ($tasks as $task)
                    @php
                        $personName = match ($task->person_type) {
                            'visitor' => $task->person_id
                                ? ($visitors->find($task->person_id)?->full_name ?? 'Visitor #'.$task->person_id)
                                : '—',
                            'member'  => $task->person_id
                                ? ($members->find($task->person_id)?->full_name ?? 'Member #'.$task->person_id)
                                : '—',
                            'family'  => $task->person_id
                                ? ($families->find($task->person_id)?->head_of_family ?? 'Family #'.$task->person_id)
                                : '—',
                            default   => 'Unknown',
                        };
                    @endphp

                    <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                        {{-- Task header --}}
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="font-semibold text-[var(--color-ink-950)]">
                                    <i class="fa-solid fa-{{ $task->task_type === 'call' ? 'phone' : ($task->task_type === 'sms' ? 'comment-sms' : ($task->task_type === 'visit' ? 'car' : ($task->task_type === 'prayer' ? 'hands-praying' : ($task->task_type === 'counseling' ? 'user-doctor' : 'map-pin')))) }} mr-1 text-blue-500"></i>
                                    {{ strtoupper(str_replace('_', ' ', $task->task_type)) }}
                                    <span class="font-normal text-slate-500">for</span>
                                    {{ $personName }}
                                    <span class="text-xs text-slate-400">({{ $task->person_type }})</span>
                                </p>
                                <p class="mt-1 flex items-center gap-3 text-xs text-slate-500">
                                    <span>
                                        <i class="fa-solid fa-user-shield mr-1 text-emerald-500"></i>
                                        {{ $task->leader?->full_name ?: 'Unassigned' }}
                                    </span>
                                    @if ($task->due_date)
                                        <span>
                                            <i class="fa-solid fa-calendar mr-1 text-amber-500"></i>
                                            Due {{ $task->due_date->format('d M Y') }}
                                        </span>
                                    @endif
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold uppercase
                                        @if ($task->priority === 'high') bg-red-100 text-red-700
                                        @elseif ($task->priority === 'medium') bg-amber-100 text-amber-700
                                        @else bg-slate-100 text-slate-600 @endif">
                                        {{ $task->priority }}
                                    </span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.status-badge :status="$task->status" />
                                {{-- Delete --}}
                                @if ($canDelete)
                                <form method="POST" action="{{ route('follow-up.tasks.destroy', $task) }}"
                                      data-confirm="Delete this task?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100"
                                            title="Delete task">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                            </div>
                        </div>

                        @if ($task->notes)
                            <p class="mt-2 text-sm text-slate-600">{{ $task->notes }}</p>
                        @endif

                        {{-- Update form --}}
                        <form class="mt-4 grid gap-2 md:grid-cols-2 xl:grid-cols-4" method="POST"
                              action="{{ route('follow-up.tasks.update', $task) }}">
                            @csrf
                            @method('PUT')
                            <select name="leader_id" class="form-input">
                                <option value="">— leader —</option>
                                @foreach ($leaders as $leader)
                                    <option value="{{ $leader->id }}" @selected((string) $task->leader_id === (string) $leader->id)>
                                        {{ $leader->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="priority" class="form-input">
                                @foreach (['low', 'medium', 'high'] as $p)
                                    <option value="{{ $p }}" @selected($task->priority === $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                            <select name="status" class="form-input">
                                @foreach (['pending' => 'Pending', 'in_progress' => 'In progress', 'completed' => 'Completed'] as $s => $l)
                                    <option value="{{ $s }}" @selected($task->status === $s)>{{ $l }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="due_date" class="form-input"
                                   value="{{ optional($task->due_date)->format('Y-m-d') }}">
                            <input name="notes" class="form-input md:col-span-2 xl:col-span-3"
                                   value="{{ $task->notes }}" placeholder="Update notes">
                            <button class="btn-secondary" type="submit">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Save
                            </button>
                        </form>

                        {{-- Add history --}}
                        <form class="mt-3 grid gap-2 md:grid-cols-[minmax(0,1fr)_auto]" method="POST"
                              action="{{ route('follow-up.history.store') }}">
                            @csrf
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <input name="action_taken" class="form-input"
                                   placeholder="Record action taken (e.g. Called, prayed with them…)">
                            <button class="btn-secondary" type="submit">
                                <i class="fa-solid fa-clock-rotate-left mr-1"></i> Add history
                            </button>
                        </form>

                        {{-- History entries --}}
                        @if ($task->history->count())
                            <div class="mt-3 space-y-1 border-t border-[var(--color-surface-100)] pt-3">
                                @foreach ($task->history->take(5) as $entry)
                                    <p class="flex items-start gap-1 text-xs text-slate-500">
                                        <i class="fa-solid fa-circle-dot mt-0.5 text-emerald-400 shrink-0"></i>
                                        <span class="text-slate-400 shrink-0">{{ $entry->created_at?->format('d M Y H:i') }}:</span>
                                        {{ $entry->action_taken }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400">
                        <i class="fa-solid fa-clipboard-list mb-2 text-3xl"></i>
                        <p>No tasks match the current filters.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $tasks->links() }}</div>
        </article>
    </section>

    {{-- JS: switch person dropdown based on person_type --}}
    <script>
        function switchPersonList(type) {
            ['visitor', 'member', 'family'].forEach(function (t) {
                var el = document.getElementById('person_id_' + t);
                if (t === type) {
                    el.classList.remove('hidden');
                    el.disabled = false;
                    el.name = 'person_id';
                } else {
                    el.classList.add('hidden');
                    el.disabled = true;
                    el.name = '';
                }
            });
        }
        // init on load
        switchPersonList(document.getElementById('person_type').value);
    </script>
</x-layouts.app>
