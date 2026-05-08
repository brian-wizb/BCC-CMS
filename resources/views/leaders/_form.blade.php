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
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Leader identity</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="full_name">
                <i class="fas fa-user-tie mr-1 opacity-50 text-xs"></i> Full name <span class="text-red-500">*</span>
            </label>
            <input id="full_name" name="full_name" class="form-input"
                   placeholder="Leader's full name"
                   value="{{ old('full_name', $leader->full_name) }}" required>
        </div>
        <div>
            <label class="form-label" for="member_id">
                <i class="fas fa-id-card mr-1 opacity-50 text-xs"></i> Linked member
            </label>
            <select id="member_id" name="member_id" class="form-input">
                <option value="">— Not linked to a member —</option>
                @foreach ($members as $m)
                    <option value="{{ $m->id }}" @selected(old('member_id', $leader->member_id) == $m->id)>
                        {{ $m->full_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="phone">
                <i class="fas fa-phone mr-1 opacity-50 text-xs"></i> Phone
            </label>
            <input id="phone" name="phone" class="form-input" placeholder="+255 712 345 678"
                   value="{{ old('phone', $leader->phone) }}">
        </div>
        <div>
            <label class="form-label" for="email">
                <i class="fas fa-envelope mr-1 opacity-50 text-xs"></i> Email
            </label>
            <input id="email" name="email" type="email" class="form-input" placeholder="leader@example.com"
                   value="{{ old('email', $leader->email) }}">
        </div>
    </div>
</div>

{{-- ── SECTION 2: Role & Zone ───────────────────────────────── --}}
<div class="mb-6">
    <div class="mb-3 flex items-center gap-2 border-b border-[var(--color-surface-200)] pb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(52,211,153,0.12);">
            <i class="fas fa-sitemap text-xs" style="color:rgba(52,211,153,0.9);"></i>
        </span>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Role &amp; assignment</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="form-label" for="role">
                <i class="fas fa-tag mr-1 opacity-50 text-xs"></i> Role / title
            </label>
            <input id="role" name="role" class="form-input"
                   placeholder="e.g. Zone Leader, Elder, Cell Group Leader"
                   value="{{ old('role', $leader->role) }}">
        </div>
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
