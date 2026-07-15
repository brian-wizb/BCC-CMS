@csrf

@php
    $nameParts = preg_split('/\s+/', trim((string) ($visitor->full_name ?? '')), -1, PREG_SPLIT_NO_EMPTY);
    $defaultFirstName = old('first_name', $nameParts[0] ?? '');
    $defaultSurname = old('surname', count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '');
    $defaultMiddleName = old('middle_name', count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : '');
@endphp

{{-- ── Visitor Information ────────────────────────────────────────── --}}
<div class="mb-8 rounded-2xl border border-[var(--color-surface-200)] p-6">
    <h4 class="mb-5 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">
        <i class="fa-solid fa-user w-4 text-center"></i> Visitor Information
    </h4>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div>
            <label class="form-label" for="first_name">First name <span class="text-red-500">*</span></label>
            <input id="first_name" name="first_name" class="form-input" required
                   value="{{ $defaultFirstName }}">
        </div>
        <div>
            <label class="form-label" for="middle_name">Middle name</label>
            <input id="middle_name" name="middle_name" class="form-input"
                   value="{{ $defaultMiddleName }}">
        </div>
        <div>
            <label class="form-label" for="surname">Surname <span class="text-red-500">*</span></label>
            <input id="surname" name="surname" class="form-input" required
                   value="{{ $defaultSurname }}">
        </div>
        <div>
            <label class="form-label" for="gender">Gender <span class="text-red-500">*</span></label>
            <select id="gender" name="gender" class="form-input" required>
                <option value="">— select —</option>
                @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('gender', $visitor->gender) === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="phone">Phone</label>
            <input id="phone" name="phone" class="form-input" placeholder="+255 712 345 678"
                   value="{{ old('phone', $visitor->phone) }}">
        </div>
        <div>
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email" class="form-input"
                   value="{{ old('email', $visitor->email) }}">
        </div>
        <div>
            <label class="form-label" for="address">Address</label>
            <input id="address" name="address" class="form-input"
                   value="{{ old('address', $visitor->address) }}">
        </div>
        <div>
            <label class="form-label" for="first_visit_date">First visit date</label>
            <input id="first_visit_date" name="first_visit_date" type="date" class="form-input"
                   value="{{ old('first_visit_date', optional($visitor->first_visit_date)->format('Y-m-d')) }}">
        </div>
    </div>
</div>

{{-- ── Church Details ───────────────────────────────────────────────── --}}
<div class="mb-8 rounded-2xl border border-[var(--color-surface-200)] p-6">
    <h4 class="mb-5 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-emerald-600">
        <i class="fa-solid fa-church w-4 text-center"></i> Church Details
    </h4>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="service_id">Service attended</label>
            <select id="service_id" name="service_id" class="form-input">
                <option value="">— select service —</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" @selected((string) old('service_id', $visitor->service_id) === (string) $service->id)>
                        {{ $service->name }} — {{ optional($service->service_date)->format('d M Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="invited_by">Invited by</label>
            <input id="invited_by" name="invited_by" class="form-input" list="members-list"
                   placeholder="Name or select member"
                   value="{{ old('invited_by', $visitor->invited_by) }}">
            <datalist id="members-list">
                @foreach ($members as $m)
                    <option value="{{ $m->full_name }}">
                @endforeach
            </datalist>
        </div>
        <div>
            <label class="form-label" for="status">Status <span class="text-red-500">*</span></label>
            <select id="status" name="status" class="form-input" required>
                @foreach (['new' => 'New', 'contacted' => 'Contacted', 'counseled' => 'Counseled', 'joined_zone' => 'Joined Zone', 'in_class' => 'In Class', 'converted' => 'Converted'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $visitor->status ?: 'new') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- ── Notes ────────────────────────────────────────────────────────── --}}
<div class="mb-8 rounded-2xl border border-[var(--color-surface-200)] p-6">
    <h4 class="mb-5 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">
        <i class="fa-solid fa-comment-alt w-4 text-center"></i> Notes
    </h4>
    <textarea id="notes" name="notes" rows="4" class="form-input w-full"
              placeholder="Any additional notes about this visitor…">{{ old('notes', $visitor->notes) }}</textarea>
</div>

<div class="flex gap-3">
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-floppy-disk mr-1"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('visitors.index') }}" class="btn-secondary">
        <i class="fa-solid fa-xmark mr-1"></i> Cancel
    </a>
</div>
