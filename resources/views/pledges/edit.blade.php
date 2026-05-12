<x-layouts.app title="Edit Pledge">
    <div class="space-y-6">

        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(139,92,246,0.12);">
                <i class="fas fa-handshake text-base" style="color:rgba(139,92,246,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit Pledge</h3>
            </div>
        </div>

        <div class="mx-auto max-w-2xl">
            <article class="surface-card p-6">
                <form method="POST" action="{{ route('pledges.update', $pledge) }}" class="space-y-5">
                    @csrf @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pledger Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="pledger_name" value="{{ old('pledger_name', $pledge->pledger_name) }}" required class="form-input w-full">
                            @error('pledger_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</label>
                            <input type="text" name="pledger_phone" value="{{ old('pledger_phone', $pledge->pledger_phone) }}" class="form-input w-full" placeholder="e.g. 0712 345 678">
                            @error('pledger_phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Campaign</label>
                            <select name="campaign_id" class="form-input w-full">
                                <option value="">— None —</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" {{ old('campaign_id', $pledge->campaign_id) == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                            @error('campaign_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Amount (Tsh.) <span class="text-rose-500">*</span></label>
                            <input type="number" name="amount" value="{{ old('amount', $pledge->amount) }}" min="0" step="0.01" required class="form-input w-full">
                            @error('amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="pledge_date" value="{{ old('pledge_date', $pledge->pledge_date) }}" required class="form-input w-full">
                            @error('pledge_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Due/End Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date', $pledge->due_date) }}" class="form-input w-full">
                            @error('due_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Notes</label>
                        <textarea name="notes" rows="3" class="form-input w-full">{{ old('notes', $pledge->notes) }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('pledges.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary flex items-center gap-1.5"><i class="fas fa-save text-xs"></i> Update Pledge</button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</x-layouts.app>
