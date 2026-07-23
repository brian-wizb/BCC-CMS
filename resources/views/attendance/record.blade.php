<x-layouts.app title="Record Attendance">
    <div class="attendance-responsive">
    <div class="mb-6 flex items-center gap-4 attendance-header">
        <a href="{{ route('attendance.index') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-600 text-white shadow">
            <i class="fa-solid fa-user-check text-xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">Record Attendance</h1>
            <p class="text-sm text-slate-500">Manually add or update individual attendance entries.</p>
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

    {{-- Entry form --}}
    <article class="surface-card p-6 mb-6">
        <h3 class="mb-4 text-base font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-plus mr-2 text-green-500"></i>Add / Update Record
        </h3>
        <form method="POST" action="{{ route('attendance.record.store') }}"
              class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @csrf
            <div>
                <label class="block text-xs text-slate-500 mb-1">Service <span class="text-red-500">*</span></label>
                <select name="service_id" class="form-input w-full" required>
                    <option value="">Select service</option>
                    @foreach ($services as $svc)
                        <option value="{{ $svc->id }}" @selected(old('service_id') == $svc->id)>
                            {{ $svc->name }} — {{ $svc->service_date?->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-slate-500 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="attendance_status" class="form-input w-full" required>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(old('attendance_status', 'present') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-slate-500 mb-1">Mode</label>
                <select name="attendance_mode" class="form-input w-full">
                    @foreach ($modes as $key => $label)
                        <option value="{{ $key }}" @selected(old('attendance_mode', 'in_person') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Person selector (member / visitor — pick one) --}}
            <div>
                <label class="block text-xs text-slate-500 mb-1">Member</label>
                <select name="member_id" class="form-input w-full">
                    <option value="">None</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" @selected(old('member_id') == $m->id)>{{ $m->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Visitor</label>
                <select name="visitor_id" class="form-input w-full">
                    <option value="">None</option>
                    @foreach ($visitors as $v)
                        <option value="{{ $v->id }}" @selected(old('visitor_id') == $v->id)>{{ $v->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-slate-500 mb-1">Zone</label>
                <select name="zone_id" class="form-input w-full">
                    <option value="">None</option>
                    @foreach ($zones as $z)
                        <option value="{{ $z->id }}" @selected(old('zone_id') == $z->id)>{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Check-In Time</label>
                <input type="time" name="check_in_time" class="form-input w-full" value="{{ old('check_in_time') }}">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Notes</label>
                <input type="text" name="notes" class="form-input w-full" placeholder="Optional note" value="{{ old('notes') }}">
            </div>

            <div class="md:col-span-2 xl:col-span-3 pt-1">
                <button class="btn-primary" type="submit">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Save Record
                </button>
            </div>
        </form>
    </article>

    {{-- Filter + table --}}
    <article class="surface-card p-6">
        <form method="GET" action="{{ route('attendance.record') }}" class="mb-5 flex flex-wrap gap-3 items-end attendance-filter-form">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Filter by Service</label>
                <select name="service_id" class="form-input">
                    <option value="">All services</option>
                    @foreach ($services as $svc)
                        <option value="{{ $svc->id }}" @selected((string) $serviceId === (string) $svc->id)>
                            {{ $svc->name }} — {{ $svc->service_date?->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Filter by Status</label>
                <select name="status" class="form-input">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-secondary" type="submit"><i class="fa-solid fa-filter mr-1"></i>Filter</button>
            @if ($serviceId || $status)
                <a href="{{ route('attendance.record') }}" class="btn-secondary text-slate-500">
                    <i class="fa-solid fa-xmark mr-1"></i>Clear
                </a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Service</th>
                        <th class="px-4 py-3">Person</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Mode</th>
                        <th class="px-4 py-3">Zone</th>
                        <th class="px-4 py-3">Check-In</th>
                        <th class="px-4 py-3">Recorded</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($records as $record)
                        <tr class="hover:bg-[var(--color-surface-50)]">
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $record->service?->name ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $record->person_name }}</td>
                            <td class="px-4 py-3 capitalize text-slate-500">{{ $record->person_type }}</td>
                            <td class="px-4 py-3"><x-ui.status-badge :status="$record->attendance_status" /></td>
                            <td class="px-4 py-3 capitalize text-slate-500">{{ str_replace('_', ' ', $record->attendance_mode) }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $record->zone?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $record->check_in_time?->format('H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">{{ $record->recorded_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('attendance.record.edit', $record) }}"
                                       class="text-[var(--color-brand-600)] hover:underline text-xs">Edit</a>
                                    <form method="POST" action="{{ route('attendance.record.destroy', $record) }}"
                                          data-confirm="Delete this record?" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:underline text-xs" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No attendance records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $records->links() }}</div>
    </article>
    </div>
</x-layouts.app>

