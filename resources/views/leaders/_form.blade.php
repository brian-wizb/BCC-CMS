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

{{-- ── SECTION 1: Identity ──────────────────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(36,184,255,0.12);">
            <i class="fas fa-user-shield text-xs" style="color:rgba(36,184,255,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Leader source member</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="member_id">
                <i class="fas fa-id-card mr-1 opacity-50 text-xs"></i> Registered member <span class="text-red-500">*</span>
            </label>
            <select id="member_id" name="member_id" class="form-input" required>
                <option value="">— Select member to create leader —</option>
                @foreach ($members as $m)
                    <option value="{{ $m->id }}" @selected(old('member_id', $leader->member_id) == $m->id)>
                        {{ $m->full_name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-400">Name, phone, and email are synced automatically from selected member.</p>
        </div>
    </div>
</div>

{{-- ── SECTION 2: Role & Zone ───────────────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(52,211,153,0.12);">
            <i class="fas fa-sitemap text-xs" style="color:rgba(52,211,153,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Assignment details</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="zone">
                <i class="fas fa-map-marker-alt mr-1 opacity-50 text-xs"></i> Zone
            </label>
            <input id="zone" name="zone" class="form-input" placeholder="e.g. North Zone"
                   value="{{ old('zone', $leader->zone) }}">
        </div>
        <div>
            <label class="form-label" for="status">
                <i class="fas fa-toggle-on mr-1 opacity-50 text-xs"></i> Status <span class="text-red-500">*</span>
            </label>
            <select id="status" name="status" class="form-input" required>
                <option value="active"   @selected(old('status', $leader->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $leader->status) === 'inactive')>Inactive</option>
            </select>
        </div>
    </div>
</div>

{{-- ── SECTION 3: Notes ─────────────────────────────────────── --}}
<div class="mb-2">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(100,116,139,0.10);">
            <i class="fas fa-comment-alt text-xs text-slate-400"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Notes</p>
    </div>
    <textarea id="notes" name="notes" rows="3" class="form-input"
              placeholder="Any additional notes about this leader…">{{ old('notes', $leader->notes) }}</textarea>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary flex items-center gap-2">
        <i class="fas fa-save text-xs"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('leaders.index') }}" class="btn-secondary flex items-center gap-2">
        <i class="fas fa-times text-xs"></i> Cancel
    </a>
</div>
