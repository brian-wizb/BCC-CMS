<x-layouts.app title="New Pledge Payment">
    <div class="space-y-6">

        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(16,185,129,0.12);">
                <i class="fas fa-money-bill-wave text-base" style="color:rgba(16,185,129,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">New Pledge Payment</h3>
            </div>
        </div>

        <div class="mx-auto max-w-2xl">
            <article class="surface-card p-6">
                <form method="POST" action="{{ route('pledge-payments.store') }}" enctype="multipart/form-data" class="space-y-5">
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
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Campaign</label>
                        <select name="campaign_id" class="form-input w-full">
                            <option value="">— None —</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                        @error('campaign_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-input w-full" placeholder="+255...">
                            @error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Invoice Number</label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" class="form-input w-full" placeholder="INV-0001">
                            @error('invoice_number')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Amount (Tsh.) <span class="text-rose-500">*</span></label>
                            <input type="number" name="amount" value="{{ old('amount') }}" min="0" step="0.01" required class="form-input w-full" placeholder="0.00">
                            @error('amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Payment Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="payment_date" value="{{ old('payment_date') }}" required class="form-input w-full">
                            @error('payment_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Payment Method <span class="text-rose-500">*</span></label>
                        <select name="method" required class="form-input w-full">
                            @foreach(['Cash','Mobile','Credit','Cheque','Bank'] as $m)
                                <option value="{{ $m }}" {{ old('method') == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                        @error('method')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Notes</label>
                        <textarea name="notes" rows="2" class="form-input w-full" placeholder="Optional…">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Attachment</label>
                        <input type="file" name="attachment" class="form-input w-full">
                        @error('attachment')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('pledge-payments.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary flex items-center gap-1.5"><i class="fas fa-save text-xs"></i> Save Payment</button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</x-layouts.app>
