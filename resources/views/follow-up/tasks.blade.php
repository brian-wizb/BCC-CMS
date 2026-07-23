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
            @php($assignmentScope = old('assignment_scope', 'individual'))
            @php($personTypeValue = in_array(old('person_type'), ['visitor', 'member'], true) ? old('person_type') : 'visitor')
            <article class="surface-card p-6">
                <h4 class="mb-5 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">
                    <i class="fa-solid fa-plus-circle w-4 text-center"></i> New Follow-up Task
                </h4>
                <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-3" method="POST"
                      action="{{ route('follow-up.tasks.store') }}" id="task-create-form">
                    @csrf

                <div class="md:col-span-2 xl:col-span-3">
                    <label class="form-label" for="assignment_scope">Assignment type <span class="text-red-500">*</span></label>
                    <select id="assignment_scope" name="assignment_scope" class="form-input" required>
                        <option value="individual" @selected($assignmentScope === 'individual')>Individual person</option>
                        <option value="multiple" @selected($assignmentScope === 'multiple')>Multiple people</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Use multiple people when one counsellor should follow up several visitors or members at once.</p>
                </div>

                {{-- Person type selector --}}
                <div id="person-type-wrap">
                    <label class="form-label" for="person_type">Person type <span class="text-red-500">*</span></label>
                    <select id="person_type" name="person_type" class="form-input">
                        <option value="visitor" @selected($personTypeValue === 'visitor')>Visitor</option>
                        <option value="member" @selected($personTypeValue === 'member')>Member</option>
                    </select>
                </div>

                {{-- Dynamic person selection --}}
                <div id="person-wrap">
                    <label class="form-label" for="person-selection-btn">Person <span class="text-red-500">*</span></label>
                    <div class="flex gap-2" id="person-selection-container">
                        <button type="button" id="person-selection-btn" class="flex-1 form-input text-left text-slate-500 hover:bg-slate-50 transition cursor-pointer">
                            <i class="fa-solid fa-user mr-2"></i><span id="person-selection-text">Select person...</span>
                        </button>
                        <span id="person-selection-count" class="inline-flex items-center px-3 rounded-lg bg-blue-50 text-blue-700 font-semibold min-w-fit">0</span>
                    </div>
                    <p id="person-hint" class="mt-1 text-xs text-slate-400">Choose one person for a single assignment.</p>
                    <div id="person-selection-display" class="mt-2 flex flex-wrap gap-2"></div>
                    
                    {{-- Hidden input fields for form submission --}}
                    <input type="hidden" id="person_id" name="person_id" value="">
                    <input type="hidden" id="person_ids_input" name="person_ids" value="">
                </div>

                {{-- Modal for multiple selection --}}
                <div id="person-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50" style="display: none;">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md mx-4 max-h-96 flex flex-col">
                        <div class="border-b border-slate-200 px-6 py-4">
                            <h5 class="font-semibold text-slate-900" id="modal-title">Select Person</h5>
                            <p class="text-xs text-slate-500 mt-1" id="modal-instructions">Choose one person for assignment.</p>
                        </div>

                        <div class="px-6 py-3 border-b border-slate-200">
                            <input type="text" id="person-search" class="form-input w-full" placeholder="Search people...">
                        </div>

                        <div id="person-list-container" class="flex-1 overflow-y-auto px-6 py-3">
                            <div class="text-center text-slate-400 text-sm py-4">Loading people...</div>
                        </div>

                        <div class="border-t border-slate-200 px-6 py-4 flex gap-2 justify-end">
                            <button type="button" id="person-modal-cancel" class="btn-secondary">
                                Cancel
                            </button>
                            <button type="button" id="person-modal-done" class="btn-primary">
                                Done
                            </button>
                        </div>
                    </div>
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
                    <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                        {{-- Task header --}}
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="font-semibold text-[var(--color-ink-950)]">
                                    <i class="fa-solid fa-{{ $task->task_type === 'call' ? 'phone' : ($task->task_type === 'sms' ? 'comment-sms' : ($task->task_type === 'visit' ? 'car' : ($task->task_type === 'prayer' ? 'hands-praying' : ($task->task_type === 'counseling' ? 'user-doctor' : 'map-pin')))) }} mr-1 text-blue-500"></i>
                                    {{ $task->task_type_label }}
                                    <span class="font-normal text-slate-500">for</span>
                                    {{ $task->target_display_name }}
                                </p>
                                <p class="mt-1 flex items-center gap-3 text-xs text-slate-500">
                                    <span>
                                        <i class="fa-solid fa-user mr-1 text-slate-400"></i>
                                        {{ $task->target_display_type }}
                                    </span>
                                    @if ($task->target_member_count > 1)
                                        <span>
                                            <i class="fa-solid fa-people-group mr-1 text-sky-500"></i>
                                            {{ $task->target_member_count }} people
                                        </span>
                                    @endif
                                    @if ($task->target_phone)
                                        <span>
                                            <i class="fa-solid fa-phone mr-1 text-slate-400"></i>
                                            {{ $task->target_phone }}
                                        </span>
                                    @endif
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

                        @if ($task->display_notes)
                            <p class="mt-2 rounded-xl bg-[var(--color-surface-50)] px-3 py-2 text-sm text-slate-600">{!! nl2br(e($task->display_notes)) !!}</p>
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
                                value="{{ $task->display_notes }}" placeholder="Update notes">
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

    {{-- JS: Modal-based person selection --}}
    <script>
        (() => {
            const assignmentScopeSelect = document.getElementById('assignment_scope');
            const personTypeSelect = document.getElementById('person_type');
            const personSelectionBtn = document.getElementById('person-selection-btn');
            const personSelectionText = document.getElementById('person-selection-text');
            const personSelectionCount = document.getElementById('person-selection-count');
            const personSelectionDisplay = document.getElementById('person-selection-display');
            const personHint = document.getElementById('person-hint');
            const personIdInput = document.getElementById('person_id');
            const personIdsInput = document.getElementById('person_ids_input');
            const personModal = document.getElementById('person-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalInstructions = document.getElementById('modal-instructions');
            const personListContainer = document.getElementById('person-list-container');
            const personSearchInput = document.getElementById('person-search');
            const personModalCancel = document.getElementById('person-modal-cancel');
            const personModalDone = document.getElementById('person-modal-done');
            const peopleEndpoint = '{{ route('follow-up.people') }}';
            
            let allPeople = [];
            let selectedPeople = @json(old('person_ids', old('person_id') ? [old('person_id')] : []));
            if (typeof selectedPeople === 'string') {
                selectedPeople = selectedPeople ? [selectedPeople] : [];
            }
            selectedPeople = selectedPeople.map(id => parseInt(id)).filter(id => !isNaN(id));

            if (!assignmentScopeSelect || !personTypeSelect || !personSelectionBtn || !personModal) {
                return;
            }

            async function loadPeople() {
                const type = personTypeSelect.value;
                personListContainer.innerHTML = '<div class="text-center text-slate-400 text-sm py-4">Loading people...</div>';

                try {
                    const response = await fetch(peopleEndpoint + '?person_type=' + encodeURIComponent(type), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) throw new Error('Failed to load people');

                    const payload = await response.json();
                    allPeople = Array.isArray(payload.data) ? payload.data : [];
                    renderPeopleList('');
                } catch (error) {
                    personListContainer.innerHTML = '<div class="text-center text-red-500 text-sm py-4">Failed to load people</div>';
                }
            }

            function renderPeopleList(searchTerm) {
                const filtered = allPeople.filter(p => 
                    p.name.toLowerCase().includes(searchTerm.toLowerCase())
                );

                if (filtered.length === 0) {
                    personListContainer.innerHTML = '<div class="text-center text-slate-400 text-sm py-4">No people found</div>';
                    return;
                }

                personListContainer.innerHTML = filtered.map(person => `
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition">
                        <input type="checkbox" class="person-checkbox" value="${person.id}" 
                               ${selectedPeople.includes(person.id) ? 'checked' : ''}>
                        <span class="flex-1">${person.name}</span>
                    </label>
                `).join('');

                document.querySelectorAll('.person-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const id = parseInt(this.value);
                        if (this.checked) {
                            if (!selectedPeople.includes(id)) selectedPeople.push(id);
                        } else {
                            selectedPeople = selectedPeople.filter(pid => pid !== id);
                        }
                    });
                });
            }

            function updateDisplayAndInputs() {
                const isMultiple = assignmentScopeSelect.value === 'multiple';
                const selectedIds = selectedPeople.map(id => parseInt(id)).filter(id => !isNaN(id));

                if (isMultiple) {
                    personIdInput.value = '';
                    personIdsInput.value = JSON.stringify(selectedIds);
                    personSelectionCount.textContent = selectedIds.length;
                    personSelectionText.textContent = selectedIds.length === 0 ? 'Select people...' : `${selectedIds.length} people selected`;
                } else {
                    const id = selectedIds.length > 0 ? selectedIds[0] : '';
                    personIdInput.value = id;
                    personIdsInput.value = '';
                    personSelectionCount.textContent = id ? '1' : '0';
                    personSelectionText.textContent = selectedIds.length > 0 ? 'Person selected' : 'Select person...';
                }

                // Update display tags
                const displayHtml = allPeople
                    .filter(p => selectedIds.includes(p.id))
                    .map(p => `
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-medium">
                            ${p.name}
                            <button type="button" class="remove-person" data-id="${p.id}" title="Remove">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </span>
                    `).join('');
                
                personSelectionDisplay.innerHTML = displayHtml;

                document.querySelectorAll('.remove-person').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const id = parseInt(btn.dataset.id);
                        selectedPeople = selectedPeople.filter(pid => pid !== id);
                        updateDisplayAndInputs();
                    });
                });
            }

            function openModal() {
                const isMultiple = assignmentScopeSelect.value === 'multiple';
                modalTitle.textContent = isMultiple ? 'Select People' : 'Select Person';
                modalInstructions.textContent = isMultiple 
                    ? 'Choose multiple people of the same type.' 
                    : 'Choose one person for assignment.';
                personModal.style.display = 'flex';
                personSearchInput.value = '';
                loadPeople();
                personSearchInput.focus();
            }

            function closeModal() {
                updateDisplayAndInputs();
                personModal.style.display = 'none';
            }

            function refreshView() {
                const isMultiple = assignmentScopeSelect.value === 'multiple';
                personHint.textContent = isMultiple 
                    ? 'Select multiple people of the same type.' 
                    : 'Choose one person for a single assignment.';
                selectedPeople = [];
                updateDisplayAndInputs();
            }

            personSelectionBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openModal();
            });

            personModalCancel.addEventListener('click', closeModal);
            personModalDone.addEventListener('click', closeModal);

            personSearchInput.addEventListener('input', (e) => {
                renderPeopleList(e.target.value);
            });

            assignmentScopeSelect.addEventListener('change', refreshView);
            personTypeSelect.addEventListener('change', () => {
                selectedPeople = [];
                updateDisplayAndInputs();
            });

            personModal.addEventListener('click', (e) => {
                if (e.target === personModal) closeModal();
            });

            refreshView();
        })();
    </script>
</x-layouts.app>
