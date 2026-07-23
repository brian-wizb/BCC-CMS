@php
    $member = $member ?? null;
    $nameParts = preg_split('/\s+/', trim((string) ($member?->full_name ?? '')), -1, PREG_SPLIT_NO_EMPTY);
    $defaultFirstName = old('first_name', $nameParts[0] ?? '');
    $defaultSurname = old('surname', count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '');
    $defaultMiddleName = old('middle_name', count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : '');

    $selectedPartnerMemberId = old('partner_member_id', $member?->partner_member_id);
    $selectedPartner = collect($partners ?? [])->firstWhere('id', (int) $selectedPartnerMemberId);
    $partnerTitheCode = $selectedPartner?->tithe_code;
    $zoneOptions = collect($zones ?? [])->filter()->values()->all();
    $selectedZone = old('zone', $member?->zone);
    $profilePictureRaw = old('profile_pic', $member?->profile_pic);
    $profilePicturePreview = null;

    if (filled($profilePictureRaw) && old('profile_pic_file') === null) {
        if (isset($member) && $member?->exists) {
            $profilePicturePreview = route('members.profile-picture', $member);
        } elseif (\Illuminate\Support\Str::startsWith($profilePictureRaw, ['http://', 'https://'])) {
            $profilePicturePreview = $profilePictureRaw;
        } elseif (\Illuminate\Support\Str::startsWith($profilePictureRaw, '/storage/')) {
            $profilePicturePreview = asset(ltrim($profilePictureRaw, '/'));
        } elseif (\Illuminate\Support\Str::startsWith($profilePictureRaw, 'storage/')) {
            $profilePicturePreview = asset($profilePictureRaw);
        } elseif (\Illuminate\Support\Str::startsWith($profilePictureRaw, 'members/profile-pictures/')) {
            $profilePicturePreview = \Illuminate\Support\Facades\Storage::disk('public')->url($profilePictureRaw);
        } else {
            $profilePicturePreview = $profilePictureRaw;
        }
    }
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
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div>
            <label class="form-label" for="first_name">
                <i class="fas fa-id-badge mr-1 opacity-50 text-xs"></i> First name <span class="text-red-500">*</span>
            </label>
            <input id="first_name" name="first_name" class="form-input" placeholder="e.g. John" value="{{ $defaultFirstName }}" required>
        </div>

        <div>
            <label class="form-label" for="middle_name">
                <i class="fas fa-id-badge mr-1 opacity-50 text-xs"></i> Middle name
            </label>
            <input id="middle_name" name="middle_name" class="form-input" placeholder="e.g. Petro" value="{{ $defaultMiddleName }}">
        </div>

        <div>
            <label class="form-label" for="surname">
                <i class="fas fa-id-badge mr-1 opacity-50 text-xs"></i> Surname <span class="text-red-500">*</span>
            </label>
            <input id="surname" name="surname" class="form-input" placeholder="e.g. Mwakipesile" value="{{ $defaultSurname }}" required>
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
                @foreach (['Single', 'Married', 'Divorced', 'Widowed', 'Separated', 'Unknown'] as $status)
                    <option value="{{ $status }}" @selected(old('marital_status', $member?->marital_status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <div id="married_details_block" class="hidden md:col-span-2 lg:col-span-3 rounded-2xl border-2 border-pink-200 bg-gradient-to-br from-pink-50 via-red-50 to-pink-50 p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3 pb-4 border-b border-pink-200">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-pink-100">
                    <i class="fas fa-ring text-base" style="color:rgba(236,72,153,0.9);"></i>
                </span>
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-pink-900">Married Details</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                {{-- Partner Member Selection --}}
                <div class="md:col-span-2 lg:col-span-1">
                    <label class="form-label" for="partner_member_id">
                        <i class="fas fa-users text-sm mr-1.5 opacity-60" style="color:rgba(59,130,246,0.8);"></i>
                        <span class="font-semibold">Link to registered partner</span>
                    </label>
                    <p class="mb-2 text-xs text-slate-500">Select your spouse from the member list</p>
                    <select id="partner_member_id" name="partner_member_id" class="form-input border-pink-200 focus:border-pink-400 focus:ring-pink-300/20">
                        <option value="">— Select partner —</option>
                        @foreach ($partners ?? [] as $partner)
                            <option
                                value="{{ $partner->id }}"
                                data-partner-name="{{ $partner->full_name }}"
                                data-partner-tithe="{{ $partner->tithe_code }}"
                                @selected((string) old('partner_member_id', $member?->partner_member_id) === (string) $partner->id)
                            >
                                {{ $partner->full_name }} {{ $partner->tithe_code ? '(Tithe: '.$partner->tithe_code.')' : '(No tithe code)' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Partner Name (Fallback) --}}
                <div id="partner_name_wrapper" class="md:col-span-2 lg:col-span-1">
                    <label class="form-label" for="partner_name">
                        <i class="fas fa-heart text-sm mr-1.5 opacity-60" style="color:rgba(239,68,68,0.8);"></i>
                        <span class="font-semibold">Partner name (if not registered)</span>
                    </label>
                    <p class="mb-2 text-xs text-slate-500">Enter name if partner is not in the system</p>
                    <input id="partner_name" name="partner_name" class="form-input border-pink-200 focus:border-pink-400 focus:ring-pink-300/20" placeholder="e.g. Neema Mwakipesile" value="{{ old('partner_name', $member?->partner_name) }}">
                </div>

                {{-- Marriage Date --}}
                <div class="md:col-span-2 lg:col-span-1">
                    <label class="form-label" for="married_date">
                        <i class="fas fa-calendar-heart text-sm mr-1.5 opacity-60" style="color:rgba(217,70,239,0.8);"></i>
                        <span class="font-semibold">Marriage date</span>
                    </label>
                    <p class="mb-2 text-xs text-slate-500">Date of marriage or civil partnership</p>
                    <input id="married_date" name="married_date" type="date" class="form-input border-pink-200 focus:border-pink-400 focus:ring-pink-300/20" value="{{ old('married_date', optional($member?->married_date)->format('Y-m-d')) }}">
                </div>

                {{-- Tithe Code Sharing --}}
                <div class="md:col-span-2 lg:col-span-1 rounded-2xl border-2 border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-4">
                    <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold text-slate-800">
                        <input
                            id="share_partner_tithe_code"
                            type="checkbox"
                            name="share_partner_tithe_code"
                            value="1"
                            class="w-5 h-5 rounded"
                            style="accent-color:rgba(251,146,60,0.9);"
                            @checked(old('share_partner_tithe_code', $member?->share_partner_tithe_code))
                        >
                        <span>
                            <i class="fas fa-link text-sm mr-1.5 opacity-70" style="color:rgba(251,146,60,0.9);"></i>
                            Link tithe code
                        </span>
                    </label>
                    <p class="mt-3 text-xs text-slate-600 leading-relaxed">
                        <i class="fas fa-info-circle mr-1.5 opacity-60"></i>
                        When checked, both partners will use the tithe code of the spouse who was registered first
                    </p>
                    <div class="mt-3 rounded-lg bg-white px-3 py-2 border border-amber-200">
                        <p class="text-xs text-slate-500 font-medium">Partner / shared tithe code:</p>
                        <p id="partner_tithe_code_text" class="text-sm font-bold text-orange-700 mt-1">{{ $partnerTitheCode ?: 'Not available' }}</p>
                    </div>
                </div>
            </div>
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
            <input id="phone" name="phone" class="form-input" placeholder="+255 754 123 456" value="{{ old('phone', $member?->phone) }}">
        </div>

        <div>
            <label class="form-label" for="email">
                <i class="fas fa-envelope mr-1 opacity-50 text-xs"></i> Email
            </label>
            <input id="email" name="email" type="email" class="form-input" placeholder="member@bcc.or.tz" value="{{ old('email', $member?->email) }}">
        </div>

        <div>
            <label class="form-label" for="zone">
                <i class="fas fa-map-marker-alt mr-1 opacity-50 text-xs"></i> Zone
            </label>
            <select id="zone" name="zone" class="form-input">
                <option value="">Select zone</option>
                @foreach ($zoneOptions as $zone)
                    <option value="{{ $zone }}" @selected($selectedZone === $zone)>{{ $zone }}</option>
                @endforeach
                @if ($selectedZone && ! in_array($selectedZone, $zoneOptions, true))
                    <option value="{{ $selectedZone }}" selected>{{ $selectedZone }} (legacy)</option>
                @endif
            </select>
        </div>

        <div>
            <label class="form-label" for="residency">
                <i class="fas fa-home mr-1 opacity-50 text-xs"></i> Residency
            </label>
            <input id="residency" name="residency" class="form-input" placeholder="e.g. Dar es Salaam" value="{{ old('residency', $member?->residency) }}">
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
            <input id="member_code" name="member_code" class="form-input" placeholder="e.g. BCC0876" value="{{ old('member_code', $member?->member_code) }}">
        </div>

        <div>
            <label class="form-label" for="tithe_code">
                <i class="fas fa-hand-holding-usd mr-1 opacity-50 text-xs"></i> Tithe code
            </label>
            <input id="tithe_code" name="tithe_code" class="form-input bg-slate-50" value="{{ old('tithe_code', $member?->tithe_code) }}" readonly>
            <p class="mt-1 text-xs text-slate-500">
                Auto-assigned as <span class="font-semibold text-slate-700">TC0XXX</span>
            </p>
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
            <input id="username" name="username" class="form-input" placeholder="e.g. john.mwakipesile" value="{{ old('username', $member?->username) }}">
        </div>

        <div class="md:col-span-2">
            <label class="form-label" for="profile_pic_file">
                <i class="fas fa-image mr-1 opacity-50 text-xs"></i> Profile picture
            </label>
            <input id="profile_pic_file" name="profile_pic_file" type="file" accept="image/*" class="form-input">
            <input id="profile_pic" name="profile_pic" type="hidden" value="{{ old('profile_pic', $member?->profile_pic) }}">
            <p class="mt-1 text-xs text-slate-500">Pick an image to upload for this member profile.</p>
            <div class="mt-3 flex items-center gap-3 rounded-xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] p-3">
                <div class="h-16 w-16 overflow-hidden rounded-xl border border-[var(--color-surface-200)] bg-white">
                    <img
                        id="profile_pic_preview"
                        src="{{ $profilePicturePreview ?: '' }}"
                        alt="Profile picture preview"
                        class="h-full w-full object-cover {{ $profilePicturePreview ? '' : 'hidden' }}"
                    >
                    <div id="profile_pic_placeholder" class="flex h-full w-full items-center justify-center text-slate-400 {{ $profilePicturePreview ? 'hidden' : '' }}">
                        <i class="fas fa-user text-lg"></i>
                    </div>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Current profile image</p>
                    <p class="mt-1 truncate text-sm text-[var(--color-ink-950)]">{{ $profilePicturePreview ?: 'No image selected' }}</p>
                </div>
            </div>
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
                <input type="checkbox" name="holy_spirit_baptised" value="1" class="accent-[var(--color-brand-500)]" @checked(old('holy_spirit_baptised', $member?->holy_spirit_baptised))>
                <i class="fas fa-fire opacity-60 text-xs"></i> Holy Spirit baptized
            </label>
        </div>
    </div>
</div>

{{-- ── SECTION 5: Employment & Education ────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(16,185,129,0.12);">
            <i class="fas fa-briefcase text-xs" style="color:rgba(16,185,129,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Employment &amp; Education</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">

        {{-- Employment status --}}
        <div>
            <label class="form-label" for="employment_status">
                <i class="fas fa-briefcase mr-1 opacity-50 text-xs"></i> Employment status
            </label>
            <select id="employment_status" name="employment_status" class="form-input">
                <option value="">Select status</option>
                @foreach (['Employed', 'Unemployed', 'Entrepreneur', 'Self-employed', 'Student', 'Retired', 'Other'] as $empStatus)
                    <option value="{{ $empStatus }}" @selected(old('employment_status', $member?->employment_status) === $empStatus)>{{ $empStatus }}</option>
                @endforeach
            </select>
        </div>

        {{-- University student checkbox --}}
        <div class="flex items-center">
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] px-4 py-3 text-sm text-slate-600 hover:border-emerald-400 transition w-full">
                <input
                    id="is_university_student"
                    type="checkbox"
                    name="is_university_student"
                    value="1"
                    class="w-5 h-5 rounded"
                    style="accent-color:rgba(16,185,129,0.9);"
                    @checked(old('is_university_student', $member?->is_university_student))
                >
                <span>
                    <i class="fas fa-graduation-cap mr-1.5 opacity-70" style="color:rgba(16,185,129,0.9);"></i>
                    University / College student
                </span>
            </label>
        </div>

        {{-- University details block (shown when checkbox ticked) --}}
        <div id="university_details_block" class="hidden md:col-span-2 rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 via-teal-50 to-emerald-50 p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3 pb-4 border-b border-emerald-200">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                    <i class="fas fa-university text-base" style="color:rgba(16,185,129,0.9);"></i>
                </span>
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-emerald-900">University / College Details</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                {{-- University selector --}}
                <div class="md:col-span-3">
                    <label class="form-label" for="university_id">
                        <i class="fas fa-university text-sm mr-1.5 opacity-60" style="color:rgba(16,185,129,0.8);"></i>
                        <span class="font-semibold">Select university / college</span>
                    </label>
                    <p class="mb-2 text-xs text-slate-500">Both Tanzania and diaspora universities are listed</p>
                    <select id="university_id" name="university_id" class="form-input border-emerald-200 focus:border-emerald-400 focus:ring-emerald-300/20">
                        <option value="">— Select university —</option>
                        @php
                            $localUnis = ($universities ?? collect())->where('type', 'local');
                            $diasporaUnis = ($universities ?? collect())->where('type', 'diaspora');
                            $otherUnis = ($universities ?? collect())->where('type', 'other');
                            $selectedUniversityId = old('university_id', $member?->university_id);
                        @endphp
                        @if ($localUnis->isNotEmpty())
                            <optgroup label="─── Tanzania Universities ───">
                                @foreach ($localUnis as $uni)
                                    <option value="{{ $uni->id }}" @selected((string) $selectedUniversityId === (string) $uni->id)>{{ $uni->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if ($diasporaUnis->isNotEmpty())
                            <optgroup label="─── Diaspora Universities ───">
                                @foreach ($diasporaUnis as $uni)
                                    <option value="{{ $uni->id }}" @selected((string) $selectedUniversityId === (string) $uni->id)>{{ $uni->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if ($otherUnis->isNotEmpty())
                            <optgroup label="─── Other ───">
                                @foreach ($otherUnis as $uni)
                                    <option value="{{ $uni->id }}" @selected((string) $selectedUniversityId === (string) $uni->id)>{{ $uni->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                {{-- Start date --}}
                <div>
                    <label class="form-label" for="university_start_date">
                        <i class="fas fa-calendar-plus text-sm mr-1.5 opacity-60" style="color:rgba(16,185,129,0.8);"></i>
                        <span class="font-semibold">Enrolment date</span>
                    </label>
                    <input id="university_start_date" name="university_start_date" type="date" class="form-input border-emerald-200 focus:border-emerald-400" value="{{ old('university_start_date', optional($member?->university_start_date)->format('Y-m-d')) }}">
                </div>

                {{-- End date --}}
                <div>
                    <label class="form-label" for="university_end_date">
                        <i class="fas fa-calendar-check text-sm mr-1.5 opacity-60" style="color:rgba(16,185,129,0.8);"></i>
                        <span class="font-semibold">Expected graduation</span>
                    </label>
                    <input id="university_end_date" name="university_end_date" type="date" class="form-input border-emerald-200 focus:border-emerald-400" value="{{ old('university_end_date', optional($member?->university_end_date)->format('Y-m-d')) }}">
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── SECTION 6: Remarks ───────────────────────────────────── --}}
<div class="mb-2">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(100,116,139,0.10);">
            <i class="fas fa-comment-alt text-xs text-slate-400"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Additional notes</p>
    </div>
    <textarea id="remarks" name="remarks" rows="4" class="form-input" placeholder="Mfano: Anaishi Kimara, Dar es Salaam; anahudhuria ibada ya Jumapili ya pili.">{{ old('remarks', $member?->remarks) }}</textarea>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary flex items-center gap-2">
        <i class="fas fa-save text-xs"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('members.index') }}" class="btn-secondary flex items-center gap-2">
        <i class="fas fa-times text-xs"></i> Cancel
    </a>
</div>

<script>
    (function () {
        const maritalStatus = document.getElementById('marital_status');
        const marriedDetailsBlock = document.getElementById('married_details_block');
        const partnerMemberSelect = document.getElementById('partner_member_id');
        const partnerNameWrapper = document.getElementById('partner_name_wrapper');
        const partnerNameInput = document.getElementById('partner_name');
        const partnerTitheCodeText = document.getElementById('partner_tithe_code_text');
        const sharePartnerTitheCode = document.getElementById('share_partner_tithe_code');
        const profilePicFileInput = document.getElementById('profile_pic_file');
        const profilePicPreview = document.getElementById('profile_pic_preview');
        const profilePicPlaceholder = document.getElementById('profile_pic_placeholder');

        if (!maritalStatus || !marriedDetailsBlock) {
            return;
        }

        const getSelectedPartnerOption = () => partnerMemberSelect?.options[partnerMemberSelect.selectedIndex] ?? null;

        const selectedPartnerTitheCode = () => {
            const option = getSelectedPartnerOption();
            return option?.dataset?.partnerTithe || '';
        };

        const syncPartnerInfo = () => {
            const hasRegisteredPartner = !!partnerMemberSelect?.value;
            const partnerCode = selectedPartnerTitheCode();

            if (partnerNameWrapper) {
                partnerNameWrapper.classList.toggle('hidden', hasRegisteredPartner);
            }

            if (partnerNameInput) {
                partnerNameInput.disabled = hasRegisteredPartner;
            }

            if (partnerTitheCodeText) {
                partnerTitheCodeText.textContent = partnerCode || 'Not available';
            }

        };

        const syncMaritalState = () => {
            const isMarried = maritalStatus.value === 'Married';
            marriedDetailsBlock.classList.toggle('hidden', !isMarried);

            if (!isMarried) {
                if (partnerMemberSelect) {
                    partnerMemberSelect.value = '';
                }
                if (partnerNameInput) {
                    partnerNameInput.disabled = false;
                }
                if (sharePartnerTitheCode) {
                    sharePartnerTitheCode.checked = false;
                }
            }

            syncPartnerInfo();
        };

        maritalStatus.addEventListener('change', syncMaritalState);
        partnerMemberSelect?.addEventListener('change', syncPartnerInfo);
        sharePartnerTitheCode?.addEventListener('change', syncPartnerInfo);

        syncMaritalState();

        // ── University student toggle ──────────────────────────────────
        const isUniversityStudentCb = document.getElementById('is_university_student');
        const universityDetailsBlock = document.getElementById('university_details_block');

        if (isUniversityStudentCb && universityDetailsBlock) {
            const syncUniversityBlock = () => {
                universityDetailsBlock.classList.toggle('hidden', !isUniversityStudentCb.checked);
            };
            isUniversityStudentCb.addEventListener('change', syncUniversityBlock);
            syncUniversityBlock();
        }

        if (profilePicFileInput && profilePicPreview && profilePicPlaceholder) {
            profilePicFileInput.addEventListener('change', () => {
                const file = profilePicFileInput.files?.[0];

                if (!file) {
                    return;
                }

                const temporaryUrl = URL.createObjectURL(file);
                profilePicPreview.src = temporaryUrl;
                profilePicPreview.classList.remove('hidden');
                profilePicPlaceholder.classList.add('hidden');
            });
        }
    })();
</script>
