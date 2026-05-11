<x-layouts.app title="Edit Campaign">
    <div class="space-y-6">

        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(245,158,11,0.12);">
                <i class="fas fa-bullhorn text-base" style="color:rgba(245,158,11,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit Campaign</h3>
            </div>
        </div>

        <div class="mx-auto max-w-2xl">
            <article class="surface-card p-6">
                <form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Campaign Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required class="form-input w-full">
                        @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Description</label>
                        <textarea name="description" rows="3" class="form-input w-full">{{ old('description', $campaign->description) }}</textarea>
                        @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date) }}" class="form-input w-full">
                            @error('start_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date) }}" class="form-input w-full">
                            @error('end_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Target Amount (Tsh.)</label>
                        <input type="number" name="target_amount" value="{{ old('target_amount', $campaign->target_amount) }}" min="0" step="0.01" class="form-input w-full">
                        @error('target_amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('campaigns.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary flex items-center gap-1.5"><i class="fas fa-save text-xs"></i> Update Campaign</button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</x-layouts.app>
