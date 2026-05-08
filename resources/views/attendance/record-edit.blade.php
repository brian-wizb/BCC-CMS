<x-layouts.app title="Edit Attendance Record">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('attendance.record', ['service_id' => $record->service_id]) }}"
           class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-600 text-white shadow">
            <i class="fa-solid fa-pen text-xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">Edit Attendance Record</h1>
            <p class="text-sm text-slate-500">
                {{ $record->person_name }} — {{ $record->service?->name }}
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 border border-red-200">
            <ul class="list-disc pl-4 space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <article class="surface-card p-6 max-w-xl">
        <form method="POST" action="{{ route('attendance.record.update', $record) }}" class="grid gap-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Service</label>
                <p class="form-input bg-[var(--color-surface-100)] text-slate-500 cursor-default w-full">
                    {{ $record->service?->name }} — {{ $record->service?->service_date?->format('d M Y') }}
                </p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Person</label>
                <p class="form-input bg-[var(--color-surface-100)] text-slate-500 cursor-default w-full capitalize">
                    {{ $record->person_name }} ({{ $record->person_type }})
                </p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Attendance Status <span class="text-red-500">*</span></label>
                <select name="attendance_status" class="form-input w-full" required>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(old('attendance_status', $record->attendance_status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Attendance Mode</label>
                <select name="attendance_mode" class="form-input w-full">
                    @foreach ($modes as $key => $label)
                        <option value="{{ $key }}" @selected(old('attendance_mode', $record->attendance_mode) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Zone</label>
                <select name="zone_id" class="form-input w-full">
                    <option value="">None</option>
                    @foreach ($zones as $z)
                        <option value="{{ $z->id }}" @selected(old('zone_id', $record->zone_id) == $z->id)>{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Check-In Time</label>
                <input type="time" name="check_in_time" class="form-input w-full"
                       value="{{ old('check_in_time', $record->check_in_time?->format('H:i')) }}">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Notes</label>
                <input type="text" name="notes" class="form-input w-full"
                       value="{{ old('notes', $record->notes) }}" placeholder="Optional note">
            </div>

            <div class="flex gap-3 pt-2">
                <button class="btn-primary" type="submit">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Save Changes
                </button>
                <a href="{{ route('attendance.record', ['service_id' => $record->service_id]) }}"
                   class="btn-secondary">Cancel</a>
            </div>
        </form>
    </article>
</x-layouts.app>
