@props(['submitLabel' => 'Save'])

<form method="POST" action="{{ ($child && $child->exists) ? route('children-ministry.update', $child) : route('children-ministry.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($child && $child->exists)
        @method('PUT')
    @endif

    {{-- ── SECTION 1: Child Personal Information ─────────────── --}}
    <div class="mb-6">
        <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(36,184,255,0.12);">
                <i class="fas fa-child text-xs" style="color:rgba(36,184,255,0.9);"></i>
            </span>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Child Information</p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="form-label" for="first_name">
                    <i class="fas fa-user mr-1 opacity-50 text-xs"></i> First name
                </label>
                <input id="first_name" name="first_name" class="form-input @error('first_name') border-red-500 @enderror" placeholder="e.g. John" value="{{ old('first_name', $child?->first_name) }}" required>
                @error('first_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label" for="middle_name">
                    <i class="fas fa-user mr-1 opacity-50 text-xs"></i> Middle name
                </label>
                <input id="middle_name" name="middle_name" class="form-input @error('middle_name') border-red-500 @enderror" placeholder="e.g. Peter" value="{{ old('middle_name', $child?->middle_name) }}">
                @error('middle_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label" for="surname">
                    <i class="fas fa-user mr-1 opacity-50 text-xs"></i> Surname
                </label>
                <input id="surname" name="surname" class="form-input @error('surname') border-red-500 @enderror" placeholder="e.g. Smith" value="{{ old('surname', $child?->surname) }}" required>
                @error('surname') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- ── SECTION 2: Child Demographics ───────────────────── --}}
    <div class="mb-6">
        <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(168,85,247,0.12);">
                <i class="fas fa-venus-mars text-xs" style="color:rgba(168,85,247,0.9);"></i>
            </span>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Demographics</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label" for="sex">
                    <i class="fas fa-venus-mars mr-1 opacity-50 text-xs"></i> Sex
                </label>
                <select id="sex" name="sex" class="form-input @error('sex') border-red-500 @enderror" required>
                    <option value="">— Select sex —</option>
                    <option value="Male" @selected(old('sex', $child?->sex) === 'Male')>Male</option>
                    <option value="Female" @selected(old('sex', $child?->sex) === 'Female')>Female</option>
                </select>
                @error('sex') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label" for="date_of_birth">
                    <i class="fas fa-calendar mr-1 opacity-50 text-xs"></i> Date of birth
                </label>
                <input id="date_of_birth" name="date_of_birth" type="date" class="form-input @error('date_of_birth') border-red-500 @enderror" value="{{ old('date_of_birth', $child?->date_of_birth?->format('Y-m-d')) }}">
                @error('date_of_birth') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- ── SECTION 3: Parent Information ───────────────────── --}}
    <div class="mb-6">
        <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(244,193,93,0.12);">
                <i class="fas fa-users text-xs" style="color:rgba(244,193,93,0.9);"></i>
            </span>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Parent Information</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label" for="parent_name">
                    <i class="fas fa-user-tie mr-1 opacity-50 text-xs"></i> Parent name
                </label>
                <input id="parent_name" name="parent_name" class="form-input @error('parent_name') border-red-500 @enderror" placeholder="e.g. John Smith" value="{{ old('parent_name', $child?->parent_name) }}" required>
                @error('parent_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label" for="parent_contact">
                    <i class="fas fa-phone mr-1 opacity-50 text-xs"></i> Parent contact
                </label>
                <input id="parent_contact" name="parent_contact" class="form-input @error('parent_contact') border-red-500 @enderror" placeholder="e.g. +255 123 456 789" value="{{ old('parent_contact', $child?->parent_contact) }}">
                @error('parent_contact') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Parent Member Selection --}}
        <div class="mt-4">
            <label class="form-label" for="parent_member_id">
                <i class="fas fa-users mr-1 opacity-50 text-xs"></i> Link to registered member
            </label>
            <p class="mb-2 text-xs text-slate-500">Select parent from registered members list (optional)</p>
            <select id="parent_member_id" name="parent_member_id" class="form-input @error('parent_member_id') border-red-500 @enderror">
                <option value="">— No registered parent —</option>
                @foreach ($members ?? [] as $member)
                    <option value="{{ $member->id }}" @selected(old('parent_member_id', $child?->parent_member_id) == $member->id)>
                        {{ $member->full_name }}
                    </option>
                @endforeach
            </select>
            @error('parent_member_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── SECTION 4: Additional Remarks ──────────────────── --}}
    <div class="mb-6">
        <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(100,116,139,0.12);">
                <i class="fas fa-sticky-note text-xs" style="color:rgba(100,116,139,0.9);"></i>
            </span>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Additional Information</p>
        </div>
        <div>
            <label class="form-label" for="remarks">
                <i class="fas fa-note-sticky mr-1 opacity-50 text-xs"></i> Remarks
            </label>
            <textarea id="remarks" name="remarks" class="form-input @error('remarks') border-red-500 @enderror" placeholder="Add any additional notes about the child..." rows="4">{{ old('remarks', $child?->remarks) }}</textarea>
            @error('remarks') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="flex gap-3">
        <button type="submit" class="btn-primary flex items-center gap-1.5">
            <i class="fas fa-check text-xs"></i> {{ $submitLabel }}
        </button>
        <a href="{{ route('children-ministry.index') }}" class="btn-secondary flex items-center gap-1.5">
            <i class="fas fa-times text-xs"></i> Cancel
        </a>
    </div>
</form>
