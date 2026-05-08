<x-layouts.app title="Attendance Services">
    {{-- Icon header --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('attendance.index') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-600 text-white shadow">
            <i class="fa-solid fa-church text-xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">Services</h1>
            <p class="text-sm text-slate-500">Manage church service sessions.</p>
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

    {{-- Add service form --}}
    <article class="surface-card p-6 mb-6">
        <h3 class="mb-4 text-base font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-plus mr-2 text-[var(--color-brand-600)]"></i>Add New Service
        </h3>
        <form method="POST" action="{{ route('attendance.services.store') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @csrf
            <div>
                <label class="block text-xs text-slate-500 mb-1">Service Name <span class="text-red-500">*</span></label>
                <input name="name" class="form-input w-full" placeholder="e.g. Sunday Morning Service" required value="{{ old('name') }}">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Service Type <span class="text-red-500">*</span></label>
                <select name="service_type" class="form-input w-full" required>
                    <option value="">Select type</option>
                    @foreach ($serviceTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('service_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Date <span class="text-red-500">*</span></label>
                <input type="date" name="service_date" class="form-input w-full" required value="{{ old('service_date') }}">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Start Time</label>
                <input type="time" name="start_time" class="form-input w-full" value="{{ old('start_time') }}">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">End Time</label>
                <input type="time" name="end_time" class="form-input w-full" value="{{ old('end_time') }}">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Attendance Mode</label>
                <select name="attendance_mode" class="form-input w-full">
                    @foreach ($modes as $key => $label)
                        <option value="{{ $key }}" @selected(old('attendance_mode', 'in_person') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Location</label>
                <input name="location" class="form-input w-full" placeholder="Main Sanctuary" value="{{ old('location') }}">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Recurrence</label>
                <select name="recurrence_rule" class="form-input w-full">
                    @foreach ($recurrenceRules as $key => $label)
                        <option value="{{ $key }}" @selected(old('recurrence_rule', 'none') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-xs text-slate-500 mb-1">Description</label>
                <input name="description" class="form-input w-full" placeholder="Optional note" value="{{ old('description') }}">
            </div>
            <div class="md:col-span-2 xl:col-span-3 pt-2">
                <button class="btn-primary" type="submit">
                    <i class="fa-solid fa-plus mr-2"></i>Create Service
                </button>
            </div>
        </form>
    </article>

    {{-- Services list --}}
    <article class="surface-card p-6">
        <h3 class="mb-4 text-base font-semibold text-[var(--color-ink-950)]">All Services</h3>
        <div class="space-y-3">
            @forelse ($services as $service)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[var(--color-surface-200)] p-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                            <i class="fa-solid fa-church text-sm"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-[var(--color-ink-950)] truncate">{{ $service->name }}</p>
                            <div class="mt-0.5 flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-slate-500">
                                <span><i class="fa-regular fa-calendar mr-1"></i>{{ $service->service_date?->format('d M Y') }}</span>
                                @if ($service->start_time)
                                    <span><i class="fa-regular fa-clock mr-1"></i>{{ $service->start_time }}{{ $service->end_time ? ' – '.$service->end_time : '' }}</span>
                                @endif
                                @if ($service->location)
                                    <span><i class="fa-solid fa-location-dot mr-1"></i>{{ $service->location }}</span>
                                @endif
                                <span>
                                    <i class="fa-solid fa-users mr-1"></i>{{ $service->attendance_records_count ?? 0 }} records
                                </span>
                                @php $stypes = config('attendance.service_types', []); @endphp
                                <span class="inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-violet-700">
                                    {{ $stypes[$service->service_type] ?? $service->service_type }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('attendance.bulk', ['service_id' => $service->id]) }}"
                           class="btn-secondary text-xs px-3 py-1.5">
                            <i class="fa-solid fa-list-check mr-1"></i>Bulk Sheet
                        </a>
                        <a href="{{ route('attendance.services.show', $service) }}"
                           class="btn-secondary text-xs px-3 py-1.5">
                            <i class="fa-solid fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('attendance.services.edit', $service) }}"
                           class="btn-secondary text-xs px-3 py-1.5">
                            <i class="fa-solid fa-pen mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('attendance.services.destroy', $service) }}"
                              onsubmit="return confirm('Delete \'{{ addslashes($service->name) }}\' and all {{ $service->attendance_records_count ?? 0 }} attendance record(s)?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn-secondary text-xs px-3 py-1.5 text-red-600 hover:bg-red-50" type="submit">
                                <i class="fa-solid fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-slate-400">No services found. Create one above.</p>
            @endforelse
        </div>
        <div class="mt-5">{{ $services->links() }}</div>
    </article>
</x-layouts.app>

