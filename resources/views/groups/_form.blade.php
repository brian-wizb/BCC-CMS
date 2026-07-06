@php
    $group = $group ?? null;
    $isEdit = isset($group) && $group->exists;
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

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="form-label" for="name">
            <i class="fas fa-tag mr-1 opacity-50 text-xs"></i> Group name <span class="text-red-500">*</span>
        </label>
        <input id="name" name="name" class="form-input" placeholder="e.g. Praise and Worship" value="{{ old('name', $group?->name) }}" required>
    </div>

    <div>
        <label class="form-label" for="icon">
            <i class="fas fa-icons mr-1 opacity-50 text-xs"></i> Icon <span class="text-xs text-slate-400">(FontAwesome class)</span>
        </label>
        <div class="flex gap-2">
            <span id="icon_preview" class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] text-indigo-500 text-base">
                <i id="icon_preview_i" class="{{ old('icon', $group?->icon ?? 'fa-users') }}"></i>
            </span>
            <input id="icon" name="icon" class="form-input flex-1" placeholder="fa-users" value="{{ old('icon', $group?->icon ?? 'fa-users') }}">
        </div>
        <p class="mt-1 text-xs text-slate-400">Browse icons at <a href="https://fontawesome.com/icons" target="_blank" class="underline hover:text-indigo-600">fontawesome.com/icons</a></p>
    </div>

    <div>
        <label class="form-label" for="color">
            <i class="fas fa-palette mr-1 opacity-50 text-xs"></i> Accent colour
        </label>
        <div class="flex gap-2 items-center">
            <input id="color" name="color" type="color" class="h-10 w-10 cursor-pointer rounded border border-[var(--color-surface-200)] p-0.5" value="{{ old('color', $group?->color ?? '#6366f1') }}">
            <input id="color_hex" class="form-input flex-1" placeholder="#6366f1" value="{{ old('color', $group?->color ?? '#6366f1') }}">
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="form-label" for="description">
            <i class="fas fa-align-left mr-1 opacity-50 text-xs"></i> Description
        </label>
        <textarea id="description" name="description" rows="3" class="form-input" placeholder="Brief description of this group's role and purpose.">{{ old('description', $group?->description) }}</textarea>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary flex items-center gap-2">
        <i class="fas fa-save text-xs"></i> {{ $submitLabel ?? ($isEdit ? 'Update group' : 'Create group') }}
    </button>
    <a href="{{ $isEdit ? route('groups.show', $group) : route('groups.index') }}" class="btn-secondary flex items-center gap-2">
        <i class="fas fa-times text-xs"></i> Cancel
    </a>
</div>

<script>
    (function () {
        const iconInput  = document.getElementById('icon');
        const iconI      = document.getElementById('icon_preview_i');
        const colorInput = document.getElementById('color');
        const colorHex   = document.getElementById('color_hex');

        if (iconInput && iconI) {
            iconInput.addEventListener('input', () => {
                iconI.className = iconInput.value.trim() || 'fa-users';
            });
        }
        if (colorInput && colorHex) {
            colorInput.addEventListener('input', () => { colorHex.value = colorInput.value; });
            colorHex.addEventListener('input', () => {
                if (/^#[0-9a-fA-F]{6}$/.test(colorHex.value)) {
                    colorInput.value = colorHex.value;
                }
            });
        }
    })();
</script>
