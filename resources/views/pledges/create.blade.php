<x-layouts.app title="New Pledge">
    <div class="space-y-6">

        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(139,92,246,0.12);">
                <i class="fas fa-handshake text-base" style="color:rgba(139,92,246,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">New Pledge</h3>
            </div>
        </div>

        <div class="mx-auto max-w-2xl">
            <article class="surface-card p-6">
                <form method="POST" action="{{ route('pledges.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pledger Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="pledger_name" value="{{ old('pledger_name') }}" required class="form-input w-full" placeholder="Full name">
                            @error('pledger_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pledger Email</label>
                            <input type="email" name="pledger_email" value="{{ old('pledger_email') }}" class="form-input w-full" placeholder="email@example.com">
                            @error('pledger_email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Campaign</label>
                            <select name="campaign_id" class="form-input w-full">
                                <option value="">— None —</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                            @error('campaign_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Amount (Tsh.) <span class="text-rose-500">*</span></label>
                            <input type="number" name="amount" value="{{ old('amount') }}" min="0" step="0.01" required class="form-input w-full" placeholder="0.00">
                            @error('amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pledge Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="pledge_date" value="{{ old('pledge_date') }}" required class="form-input w-full">
                        @error('pledge_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Notes</label>
                        <textarea name="notes" rows="3" class="form-input w-full" placeholder="Optional notes…">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('pledges.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary flex items-center gap-1.5"><i class="fas fa-save text-xs"></i> Save Pledge</button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</x-layouts.app>
