<x-layouts.app title="Record Missed Pledge">
    <div class="space-y-6">

        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(239,68,68,0.12);">
                <i class="fas fa-calendar-times text-base" style="color:rgba(239,68,68,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Record Missed Pledge</h3>
            </div>
        </div>

        <div class="mx-auto max-w-2xl">
            <article class="surface-card p-6">
                <form method="POST" action="{{ route('missed-pledges.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pledge <span class="text-rose-500">*</span></label>
                        <select name="pledge_id" required class="form-input w-full">
                            <option value="">— Select Pledge —</option>
                            @foreach($pledges as $pledge)
                                <option value="{{ $pledge->id }}" {{ old('pledge_id') == $pledge->id ? 'selected' : '' }}>
                                    {{ $pledge->pledger_name }} — Tsh. {{ number_format($pledge->amount, 2) }} ({{ $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('d M Y') : 'no date' }})
                                </option>
                            @endforeach
                        </select>
                        @error('pledge_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Missed Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="missed_date" value="{{ old('missed_date') }}" required class="form-input w-full">
                        @error('missed_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Reason</label>
                        <textarea name="reason" rows="3" class="form-input w-full" placeholder="Explain why the pledge was missed…">{{ old('reason') }}</textarea>
                        @error('reason')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('missed-pledges.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary flex items-center gap-1.5"><i class="fas fa-save text-xs"></i> Save Record</button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</x-layouts.app>
