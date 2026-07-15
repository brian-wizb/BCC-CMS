@php $family = $family ?? null; @endphp
@php
    $zoneOptions = collect($zones ?? [])->filter()->values()->all();
    $selectedZone = old('zone', $family?->zone);
@endphp

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

{{-- ── SECTION 1: Family identity ──────────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(36,184,255,0.12);">
            <i class="fas fa-home text-xs" style="color:rgba(36,184,255,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Family identity</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="head_of_family">
                <i class="fas fa-user-tie mr-1 opacity-50 text-xs"></i> Head of family <span class="text-red-500">*</span>
            </label>
            <input id="head_of_family" name="head_of_family" class="form-input"
                   list="hof-suggestions"
                   placeholder="Type a name or select a registered member"
                   value="{{ old('head_of_family', $family?->head_of_family) }}" required>
            <datalist id="hof-suggestions">
                @foreach ($members ?? [] as $m)
                    <option value="{{ $m->full_name }}">
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-slate-400">Start typing to suggest from registered members, or enter any name.</p>
        </div>
        <div>
            <label class="form-label" for="gender">
                <i class="fas fa-venus-mars mr-1 opacity-50 text-xs"></i> Gender <span class="text-red-500">*</span>
            </label>
            <select id="gender" name="gender" class="form-input" required>
                <option value="">Select gender</option>
                @foreach (['Male', 'Female'] as $genderOption)
                    <option value="{{ $genderOption }}" @selected(old('gender', $family?->gender) === $genderOption)>{{ $genderOption }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="phone">
                <i class="fas fa-phone mr-1 opacity-50 text-xs"></i> Phone
            </label>
            <input id="phone" name="phone" class="form-input" placeholder="+255 712 345 678"
                   value="{{ old('phone', $family?->phone) }}">
        </div>
        <div>
            <label class="form-label" for="joined_date">
                <i class="fas fa-calendar-plus mr-1 opacity-50 text-xs"></i> Date joined church
            </label>
            <input id="joined_date" name="joined_date" type="date" class="form-input"
                   value="{{ old('joined_date', optional($family?->joined_date)->format('Y-m-d')) }}">
        </div>
    </div>
</div>

{{-- ── SECTION 2: Location & grouping ──────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(52,211,153,0.12);">
            <i class="fas fa-map-marked-alt text-xs" style="color:rgba(52,211,153,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Location &amp; grouping</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
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
            <label class="form-label" for="home_cell_group">
                <i class="fas fa-users mr-1 opacity-50 text-xs"></i> Home cell group
            </label>
            <input id="home_cell_group" name="home_cell_group" class="form-input" placeholder="e.g. Cell Group A"
                   value="{{ old('home_cell_group', $family?->home_cell_group) }}">
        </div>
        <div class="md:col-span-2">
            <label class="form-label" for="address">
                <i class="fas fa-map-pin mr-1 opacity-50 text-xs"></i> Physical address
            </label>
            <input id="address" name="address" class="form-input" placeholder="Street / estate / area"
                   value="{{ old('address', $family?->address) }}">
        </div>
    </div>
</div>

{{-- ── SECTION 3: Family members ────────────────────────────── --}}
@php
    $selectedIds = old('member_ids', $linkedIds ?? []);
    $guests      = old('guest_members', $family?->guest_members ?? []);
    $membersJson = ($members ?? collect())
        ->map(fn($m) => ['id' => $m->id, 'name' => $m->full_name, 'gender' => $m->gender ?? ''])
        ->values()
        ->toJson();
@endphp
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(167,139,250,0.12);">
            <i class="fas fa-users text-xs" style="color:rgba(167,139,250,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Family members</p>
    </div>

    {{-- Hidden inputs for selected registered member IDs (managed by JS) --}}
    <div id="member-id-inputs">
        @foreach ((array) $selectedIds as $sid)
            <input type="hidden" name="member_ids[]" value="{{ $sid }}">
        @endforeach
    </div>

    {{-- Registered member picker --}}
    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-user-check mr-1 opacity-50 text-xs"></i> Add registered member
        </label>
        <div class="flex gap-2">
            <select id="member-picker" class="form-input flex-1 text-sm">
                <option value="">— Select a member to add —</option>
                @foreach ($members ?? [] as $m)
                    <option value="{{ $m->id }}" data-name="{{ $m->full_name }}" data-gender="{{ $m->gender ?? '' }}">
                        {{ $m->full_name }}{{ $m->gender ? ' ('.$m->gender.')' : '' }}
                    </option>
                @endforeach
            </select>
            <button type="button" onclick="addMember()"
                    class="btn-secondary flex items-center gap-1.5 px-3 text-sm">
                <i class="fas fa-plus text-xs"></i> Add
            </button>
        </div>
        {{-- Selected registered member chips --}}
        <div id="member-chips" class="mt-2 flex flex-wrap gap-2">
            @foreach ((array) $selectedIds as $sid)
                @php $sm = ($members ?? collect())->firstWhere('id', $sid); @endphp
                @if ($sm)
                    <span class="chip-registered inline-flex items-center gap-1.5 rounded-full border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] px-3 py-1 text-xs font-medium text-[var(--color-ink-950)]"
                          data-id="{{ $sm->id }}">
                        <i class="fas fa-user text-[10px] opacity-60"></i>
                        {{ $sm->full_name }}
                        <button type="button" onclick="removeMember(this, {{ $sm->id }})"
                                class="ml-0.5 text-slate-400 hover:text-red-500 transition">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </span>
                @endif
            @endforeach
        </div>
        <p class="mt-1 text-xs text-slate-400">Members added here will be linked to this family.</p>
    </div>

    {{-- Non-registered / guest member picker --}}
    <div>
        <label class="form-label">
            <i class="fas fa-user-plus mr-1 opacity-50 text-xs"></i> Add non-registered member
        </label>
        <div class="flex gap-2">
            <input type="text" id="guest-input" class="form-input flex-1 text-sm" placeholder="Type full name and press Add">
            <button type="button" onclick="addGuest()"
                    class="btn-secondary flex items-center gap-1.5 px-3 text-sm">
                <i class="fas fa-plus text-xs"></i> Add
            </button>
        </div>
        {{-- Hidden inputs + chips for guest members --}}
        <div id="guest-id-inputs">
            @foreach ((array) $guests as $g)
                @if (!empty($g))
                    <input type="hidden" name="guest_members[]" value="{{ $g }}">
                @endif
            @endforeach
        </div>
        <div id="guest-chips" class="mt-2 flex flex-wrap gap-2">
            @foreach ((array) $guests as $g)
                @if (!empty($g))
                    <span class="chip-guest inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800">
                        <i class="fas fa-user-slash text-[10px] opacity-60"></i>
                        {{ $g }}
                        <button type="button" onclick="removeGuest(this, '{{ addslashes($g) }}')"
                                class="ml-0.5 text-amber-400 hover:text-red-500 transition">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </span>
                @endif
            @endforeach
        </div>
        <p class="mt-1 text-xs text-slate-400">For family members who are not registered in the system.</p>
    </div>
</div>

{{-- ── SECTION 4: Notes ─────────────────────────────────────── --}}
<div class="mb-2">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(100,116,139,0.10);">
            <i class="fas fa-comment-alt text-xs text-slate-400"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Notes</p>
    </div>
    <textarea id="remarks" name="remarks" rows="3" class="form-input"
              placeholder="Pastoral notes, prayer needs, special circumstances…">{{ old('remarks', $family?->remarks) }}</textarea>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary flex items-center gap-2">
        <i class="fas fa-save text-xs"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('families.index') }}" class="btn-secondary flex items-center gap-2">
        <i class="fas fa-times text-xs"></i> Cancel
    </a>
</div>

<script>
// ── Registered members ────────────────────────────────────────
var selectedIds = {!! json_encode(array_map('intval', (array) $selectedIds)) !!};

function addMember() {
    var picker = document.getElementById('member-picker');
    var id     = parseInt(picker.value);
    var name   = picker.options[picker.selectedIndex]?.dataset.name ?? '';
    var gender = picker.options[picker.selectedIndex]?.dataset.gender ?? '';

    if (!id || selectedIds.indexOf(id) !== -1) { picker.value = ''; return; }
    selectedIds.push(id);

    // hidden input
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'member_ids[]'; inp.value = id; inp.id = 'mid-' + id;
    document.getElementById('member-id-inputs').appendChild(inp);

    // chip
    var chip = document.createElement('span');
    chip.className = 'chip-registered inline-flex items-center gap-1.5 rounded-full border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] px-3 py-1 text-xs font-medium text-[var(--color-ink-950)]';
    chip.dataset.id = id;
    chip.innerHTML =
        '<i class="fas fa-user text-[10px] opacity-60"></i> ' + name +
        (gender ? ' <span class="text-slate-400">(' + gender + ')</span>' : '') +
        ' <button type="button" onclick="removeMember(this,' + id + ')" class="ml-0.5 text-slate-400 hover:text-red-500 transition"><i class="fas fa-times text-[10px]"></i></button>';
    document.getElementById('member-chips').appendChild(chip);

    picker.value = '';
}

function removeMember(btn, id) {
    selectedIds = selectedIds.filter(function(x){ return x !== id; });
    var inp = document.getElementById('mid-' + id);
    if (inp) inp.remove();
    btn.closest('.chip-registered').remove();
}

// ── Guest members ─────────────────────────────────────────────
function addGuest() {
    var input = document.getElementById('guest-input');
    var name  = input.value.trim();
    if (!name) return;

    // hidden input
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'guest_members[]'; inp.value = name;
    document.getElementById('guest-id-inputs').appendChild(inp);

    // chip
    var chip = document.createElement('span');
    chip.className = 'chip-guest inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800';
    chip.innerHTML =
        '<i class="fas fa-user-slash text-[10px] opacity-60"></i> ' + name +
        ' <button type="button" onclick="removeGuest(this)" class="ml-0.5 text-amber-400 hover:text-red-500 transition"><i class="fas fa-times text-[10px]"></i></button>';
    document.getElementById('guest-chips').appendChild(chip);

    input.value = '';
    input.focus();
}

function removeGuest(btn) {
    var chip  = btn.closest('.chip-guest');
    var name  = chip.textContent.trim().replace(/×$/, '').trim();
    // remove matching hidden input
    document.querySelectorAll('#guest-id-inputs input').forEach(function(inp){
        if (inp.value === name.replace(/\s+/g,' ').trim()) inp.remove();
    });
    chip.remove();
}

// Allow pressing Enter in guest input
document.getElementById('guest-input').addEventListener('keydown', function(e){
    if (e.key === 'Enter') { e.preventDefault(); addGuest(); }
});
</script>
