<x-layouts.app title="Edit Service">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('attendance.services') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-600 text-white shadow">
            <i class="fa-solid fa-pen text-xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">Edit Service</h1>
            <p class="text-sm text-slate-500">{{ $service->name }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 border border-red-200">
            <ul class="list-disc pl-4 space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <article class="surface-card p-6 max-w-3xl">
        <form method="POST" action="{{ route('attendance.services.update', $service) }}"
              class="grid gap-4 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Service Name <span class="text-red-500">*</span></label>
                <input name="name" class="form-input w-full" required value="{{ old('name', $service->name) }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Service Type <span class="text-red-500">*</span></label>
                <select name="service_type" class="form-input w-full" required>
                    @foreach ($serviceTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('service_type', $service->service_type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Date <span class="text-red-500">*</span></label>
                <input type="date" name="service_date" class="form-input w-full" required
                       value="{{ old('service_date', $service->service_date?->format('Y-m-d')) }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Attendance Mode</label>
                <select name="attendance_mode" class="form-input w-full">
                    @foreach ($modes as $key => $label)
                        <option value="{{ $key }}" @selected(old('attendance_mode', $service->attendance_mode) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Start Time</label>
                <input type="time" name="start_time" class="form-input w-full"
                       value="{{ old('start_time', $service->start_time) }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">End Time</label>
                <input type="time" name="end_time" class="form-input w-full"
                       value="{{ old('end_time', $service->end_time) }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Location</label>
                <input name="location" class="form-input w-full" placeholder="Main Sanctuary"
                       value="{{ old('location', $service->location) }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Recurrence</label>
                <select name="recurrence_rule" class="form-input w-full">
                    @foreach ($recurrenceRules as $key => $label)
                        <option value="{{ $key }}" @selected(old('recurrence_rule', $service->recurrence_rule ?? 'none') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">Description</label>
                <textarea name="description" class="form-input w-full" rows="2">{{ old('description', $service->description) }}</textarea>
            </div>

            <div class="md:col-span-2 flex gap-3 pt-2">
                <button class="btn-primary" type="submit">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Save Changes
                </button>
                <a href="{{ route('attendance.services') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </article>
</x-layouts.app>
