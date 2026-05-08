@php
    $member = $member ?? null;
@endphp

{{-- Validation errors --}}
@if ($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="mb-1 flex items-center gap-1.5 font-semibold">
            <i class="fas fa-exclamation-circle"></i> Please fix the following errors:
        </p>
        <ul class="list-inside list-disc space-y-0.5 pl-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── SECTION 1: Personal information ─────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(36,184,255,0.12);">
            <i class="fas fa-user text-xs" style="color:rgba(36,184,255,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Personal information</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="full_name">
                <i class="fas fa-id-badge mr-1 opacity-50 text-xs"></i> Full name <span class="text-red-500">*</span>
            </label>
            <input id="full_name" name="full_name" class="form-input" placeholder="e.g. John Doe" value="{{ old('full_name', $member?->full_name) }}" required>
        </div>

        <div>
            <label class="form-label" for="gender">
                <i class="fas fa-venus-mars mr-1 opacity-50 text-xs"></i> Gender <span class="text-red-500">*</span>
            </label>
            <select id="gender" name="gender" class="form-input" required>
                <option value="">Select gender</option>
                @foreach (['Male', 'Female'] as $gender)
                    <option value="{{ $gender }}" @selected(old('gender', $member?->gender) === $gender)>{{ $gender }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" for="date_of_birth">
                <i class="fas fa-birthday-cake mr-1 opacity-50 text-xs"></i> Date of birth
            </label>
            <input id="date_of_birth" name="date_of_birth" type="date" class="form-input" value="{{ old('date_of_birth', optional($member?->date_of_birth)->format('Y-m-d')) }}">
        </div>

        <div>
            <label class="form-label" for="marital_status">
                <i class="fas fa-ring mr-1 opacity-50 text-xs"></i> Marital status
            </label>
            <select id="marital_status" name="marital_status" class="form-input">
                <option value="">Select status</option>
                @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                    <option value="{{ $status }}" @selected(old('marital_status', $member?->marital_status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" for="partner_name">
                <i class="fas fa-heart mr-1 opacity-50 text-xs"></i> Partner name
            </label>
            <input id="partner_name" name="partner_name" class="form-input" placeholder="Spouse / partner name" value="{{ old('partner_name', $member?->partner_name) }}">
        </div>

        <div>
            <label class="form-label" for="married_date">
                <i class="fas fa-calendar-heart mr-1 opacity-50 text-xs"></i> Married date
            </label>
            <input id="married_date" name="married_date" type="date" class="form-input" value="{{ old('married_date', optional($member?->married_date)->format('Y-m-d')) }}">
        </div>
    </div>
</div>

{{-- ── SECTION 2: Contact & Location ───────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(52,211,153,0.12);">
            <i class="fas fa-address-card text-xs" style="color:rgba(52,211,153,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Contact &amp; location</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="phone">
                <i class="fas fa-phone mr-1 opacity-50 text-xs"></i> Phone
            </label>
            <input id="phone" name="phone" class="form-input" placeholder="+1 234 567 8900" value="{{ old('phone', $member?->phone) }}">
        </div>

        <div>
            <label class="form-label" for="email">
                <i class="fas fa-envelope mr-1 opacity-50 text-xs"></i> Email
            </label>
            <input id="email" name="email" type="email" class="form-input" placeholder="member@example.com" value="{{ old('email', $member?->email) }}">
        </div>

        <div>
            <label class="form-label" for="zone">
                <i class="fas fa-map-marker-alt mr-1 opacity-50 text-xs"></i> Zone
            </label>
            <input id="zone" name="zone" class="form-input" placeholder="e.g. North Zone" value="{{ old('zone', $member?->zone) }}">
        </div>

        <div>
            <label class="form-label" for="residency">
                <i class="fas fa-home mr-1 opacity-50 text-xs"></i> Residency
            </label>
            <input id="residency" name="residency" class="form-input" placeholder="e.g. Nairobi" value="{{ old('residency', $member?->residency) }}">
        </div>
    </div>
</div>

{{-- ── SECTION 3: Church records ────────────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(167,139,250,0.12);">
            <i class="fas fa-church text-xs" style="color:rgba(167,139,250,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Church records</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="member_code">
                <i class="fas fa-id-card mr-1 opacity-50 text-xs"></i> Member ID
            </label>
            <input id="member_code" name="member_code" class="form-input" placeholder="e.g. BCC-0042" value="{{ old('member_code', $member?->member_code) }}">
        </div>

        <div>
            <label class="form-label" for="tithe_code">
                <i class="fas fa-hand-holding-usd mr-1 opacity-50 text-xs"></i> Tithe code
            </label>
            <input id="tithe_code" name="tithe_code" class="form-input" placeholder="e.g. T-0042" value="{{ old('tithe_code', $member?->tithe_code) }}">
        </div>

        <div>
            <label class="form-label" for="membership_date">
                <i class="fas fa-calendar-plus mr-1 opacity-50 text-xs"></i> Membership date
            </label>
            <input id="membership_date" name="membership_date" type="date" class="form-input" value="{{ old('membership_date', optional($member?->membership_date)->format('Y-m-d')) }}">
        </div>

        <div>
            <label class="form-label" for="username">
                <i class="fas fa-at mr-1 opacity-50 text-xs"></i> Username
            </label>
            <input id="username" name="username" class="form-input" placeholder="System login username" value="{{ old('username', $member?->username) }}">
        </div>

        <div class="md:col-span-2">
            <label class="form-label" for="family_id">
                <i class="fas fa-home mr-1 opacity-50 text-xs"></i> Family
            </label>
            <select id="family_id" name="family_id" class="form-input">
                <option value="">— No family assigned —</option>
                @foreach ($families ?? [] as $fam)
                    <option value="{{ $fam->id }}" @selected(old('family_id', $member?->family_id) == $fam->id)>
                        {{ $fam->head_of_family }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="form-label" for="profile_pic">
                <i class="fas fa-image mr-1 opacity-50 text-xs"></i> Profile picture URL
            </label>
            <input id="profile_pic" name="profile_pic" class="form-input" placeholder="https://..." value="{{ old('profile_pic', $member?->profile_pic) }}">
        </div>
    </div>
</div>

{{-- ── SECTION 4: Spiritual milestones ─────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(244,193,93,0.12);">
            <i class="fas fa-star text-xs" style="color:rgba(244,193,93,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Spiritual milestones</p>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] px-4 py-3 text-sm text-slate-600 hover:border-[var(--color-brand-500)] transition">
            <input type="checkbox" name="is_born_again" value="1" class="accent-[var(--color-brand-500)]" @checked(old('is_born_again', $member?->is_born_again))>
            <i class="fas fa-dove opacity-60 text-xs"></i> Born again
        </label>

        <div>
            <label class="form-label" for="born_again_date">
                <i class="fas fa-calendar-check mr-1 opacity-50 text-xs"></i> Born again date
            </label>
            <input id="born_again_date" name="born_again_date" type="date" class="form-input" value="{{ old('born_again_date', optional($member?->born_again_date)->format('Y-m-d')) }}">
        </div>

        <div class="md:col-start-1">
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] px-4 py-3 text-sm text-slate-600 hover:border-[var(--color-brand-500)] transition">
                <input type="checkbox" name="is_baptized" value="1" class="accent-[var(--color-brand-500)]" @checked(old('is_baptized', $member?->is_baptized))>
                <i class="fas fa-water opacity-60 text-xs"></i> Baptized
            </label>
        </div>

        <div>
            <label class="form-label" for="baptized_date">
                <i class="fas fa-calendar-check mr-1 opacity-50 text-xs"></i> Baptized date
            </label>
            <input id="baptized_date" name="baptized_date" type="date" class="form-input" value="{{ old('baptized_date', optional($member?->baptized_date)->format('Y-m-d')) }}">
        </div>

        <div class="md:col-start-1">
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] px-4 py-3 text-sm text-slate-600 hover:border-[var(--color-brand-500)] transition">
                <input type="checkbox" name="holy_spirit_baptised" value="1" class="accent-[var(--color-brand-500)]" @checked(old('holy_spirit_baptised', $member?->holy_spirit_baptised))>
                <i class="fas fa-fire opacity-60 text-xs"></i> Holy Spirit baptized
            </label>
        </div>
    </div>
</div>

{{-- ── SECTION 5: Remarks ───────────────────────────────────── --}}
<div class="mb-2">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(100,116,139,0.10);">
            <i class="fas fa-comment-alt text-xs text-slate-400"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Additional notes</p>
    </div>
    <textarea id="remarks" name="remarks" rows="4" class="form-input" placeholder="Any additional notes about this member…">{{ old('remarks', $member?->remarks) }}</textarea>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary flex items-center gap-2">
        <i class="fas fa-save text-xs"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('members.index') }}" class="btn-secondary flex items-center gap-2">
        <i class="fas fa-times text-xs"></i> Cancel
    </a>
</div>
