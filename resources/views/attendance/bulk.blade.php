<x-layouts.app title="Bulk Attendance Sheet">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('attendance.index') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow">
            <i class="fa-solid fa-list-check text-xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">Bulk Attendance Sheet</h1>
            <p class="text-sm text-slate-500">Select a service, then mark attendance for all members at once.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 border border-green-200">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 border border-red-200">
            <ul class="list-disc pl-4 space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Service selector --}}
    <article class="surface-card p-5 mb-6">
        <form method="GET" action="{{ route('attendance.bulk') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-52">
                <label class="block text-xs text-slate-500 mb-1">Select Service</label>
                <select name="service_id" class="form-input w-full" required onchange="this.form.submit()">
                    <option value="">— Choose a service —</option>
                    @foreach ($services as $s)
                        <option value="{{ $s->id }}" @selected((string) ($service?->id) === (string) $s->id)>
                            {{ $s->name }} — {{ $s->service_date?->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </article>

    @if ($service && $members->isNotEmpty())
    {{-- Bulk attendance form --}}
    <form method="POST" action="{{ route('attendance.bulk.store') }}">
        @csrf

        <input type="hidden" name="service_id" value="{{ $service->id }}">

        {{-- Mode + Zone universal defaults --}}
        <article class="surface-card p-5 mb-5 grid gap-4 md:grid-cols-3">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Attendance Mode for all</label>
                <select name="attendance_mode" class="form-input w-full">
                    @foreach ($modes as $key => $label)
                        <option value="{{ $key }}" @selected($key === 'in_person')>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2 md:col-span-2">
                <button type="button" id="markAllPresent"
                    class="btn-secondary text-sm border-green-400 text-green-700 hover:bg-green-50">
                    <i class="fa-solid fa-check mr-1"></i>Mark All Present
                </button>
                <button type="button" id="markAllAbsent"
                    class="btn-secondary text-sm border-red-300 text-red-600 hover:bg-red-50">
                    <i class="fa-solid fa-xmark mr-1"></i>Mark All Absent
                </button>
            </div>
        </article>

        {{-- Member grid --}}
        <article class="surface-card overflow-hidden mb-6">
            <div class="flex items-center justify-between px-5 py-3 border-b border-[var(--color-surface-200)]">
                <h3 class="text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-users mr-2 text-blue-500"></i>{{ $members->count() }} Active Members
                </h3>
                <span class="text-xs text-slate-500">Service: <strong>{{ $service->name }}</strong> — {{ $service->service_date?->format('d M Y') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Zone</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-center">Check-In</th>
                            <th class="px-4 py-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white" id="bulkBody">
                        @foreach ($members as $idx => $member)
                            @php $prevStatus = $existing->get($member->id) ?? 'present'; @endphp
                            <tr class="bulk-row hover:bg-[var(--color-surface-50)] transition-colors
                                @if($prevStatus === 'absent') row-absent
                                @elseif($prevStatus === 'late') row-late
                                @else row-present @endif"
                                data-idx="{{ $idx }}">
                                <td class="px-4 py-2 text-slate-400 text-xs">{{ $idx + 1 }}</td>
                                <td class="px-4 py-2 font-medium text-[var(--color-ink-950)]">
                                    {{ $member->full_name }}
                                    <input type="hidden" name="records[{{ $idx }}][member_id]" value="{{ $member->id }}">
                                </td>
                                <td class="px-4 py-2 text-slate-500 text-xs">{{ $member->zone ?: '—' }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach ($statuses as $key => $label)
                                            <label class="status-btn cursor-pointer select-none">
                                                <input type="radio"
                                                       name="records[{{ $idx }}][attendance_status]"
                                                       value="{{ $key }}"
                                                       class="sr-only status-radio"
                                                       @checked($prevStatus === $key)
                                                       onchange="updateRowStyle(this)">
                                                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium border
                                                    @if($key === 'present') border-green-400 text-green-700 peer-checked:bg-green-100
                                                    @elseif($key === 'late') border-amber-400 text-amber-700
                                                    @elseif($key === 'excused') border-blue-400 text-blue-700
                                                    @else border-red-400 text-red-700 @endif">
                                                    {{ $label }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <input type="time" name="records[{{ $idx }}][check_in_time]"
                                           class="form-input text-xs w-28 py-1">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="records[{{ $idx }}][notes]"
                                           class="form-input text-xs w-full py-1" placeholder="Optional note">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <div class="flex gap-3">
            <button class="btn-primary" type="submit">
                <i class="fa-solid fa-floppy-disk mr-2"></i>Save All Attendance
            </button>
            <a href="{{ route('attendance.services') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
    @elseif ($service)
        <div class="surface-card p-8 text-center text-slate-400">
            <i class="fa-solid fa-users text-3xl mb-3 block"></i>
            No active members found to record attendance for.
        </div>
    @else
        <div class="surface-card p-8 text-center text-slate-400">
            <i class="fa-solid fa-hand-point-up text-3xl mb-3 block"></i>
            Select a service above to load the attendance sheet.
        </div>
    @endif

    @push('scripts')
    <script>
        const statusColors = {
            present: 'bg-green-50',
            late:    'bg-amber-50',
            excused: 'bg-blue-50',
            absent:  'bg-red-50',
        };

        function updateRowStyle(radio) {
            const row = radio.closest('tr');
            Object.values(statusColors).forEach(c => row.classList.remove(c));
            row.classList.add(statusColors[radio.value] ?? '');

            // Highlight active radio label
            row.querySelectorAll('.status-btn span').forEach(sp => {
                sp.style.fontWeight = 'normal';
                sp.style.opacity    = '0.7';
            });
            radio.nextElementSibling.style.fontWeight = '700';
            radio.nextElementSibling.style.opacity    = '1';
        }

        // Initialise highlight on page load
        document.querySelectorAll('.status-radio:checked').forEach(r => updateRowStyle(r));

        document.getElementById('markAllPresent')?.addEventListener('click', () => {
            document.querySelectorAll('.status-radio[value="present"]').forEach(r => {
                r.checked = true;
                updateRowStyle(r);
            });
        });
        document.getElementById('markAllAbsent')?.addEventListener('click', () => {
            document.querySelectorAll('.status-radio[value="absent"]').forEach(r => {
                r.checked = true;
                updateRowStyle(r);
            });
        });
    </script>
    @endpush
</x-layouts.app>
