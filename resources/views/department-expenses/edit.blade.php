<x-layouts.app title="Edit Department Expense">
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(249,115,22,0.12);">
                    <i class="fas fa-pen text-base" style="color:rgba(249,115,22,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance &rsaquo; Dept Expenses</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit Department Expense</h3>
                </div>
            </div>
            <a href="{{ route('department-expenses.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>

        <article class="surface-card p-6">
            <form action="{{ route('department-expenses.update', $record) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-building mr-1 opacity-60"></i>Department <span class="text-rose-500">*</span>
                        </label>
                        <select name="department" class="form-input w-full" required>
                            <option value="">— Select department —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" @selected(old('department', $record->department) === $dept)>{{ $dept }}</option>
                            @endforeach
                        </select>
                        @error('department')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-tag mr-1 opacity-60"></i>Expense Description <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="expense" class="form-input w-full"
                            value="{{ old('expense', $record->expense) }}" placeholder="e.g. Office supplies" required>
                        @error('expense')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-credit-card mr-1 opacity-60"></i>Payment Method <span class="text-rose-500">*</span>
                        </label>
                        <select name="payment_method" class="form-input w-full" required>
                            @foreach($paymentMethods as $m)
                                <option value="{{ $m }}" @selected(old('payment_method', $record->payment_method) === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-hashtag mr-1 opacity-60"></i>Reference No.
                        </label>
                        <input type="text" name="reference_no" class="form-input w-full"
                            value="{{ old('reference_no', $record->reference_no) }}" placeholder="Transaction number">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-coins mr-1 opacity-60"></i>Amount (Tsh.) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="1" min="0" name="amount" class="form-input w-full"
                            value="{{ old('amount', $record->amount) }}" placeholder="0" required>
                        @error('amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-calendar-alt mr-1 opacity-60"></i>Expense Date <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="expense_date" class="form-input w-full"
                            value="{{ old('expense_date', \Carbon\Carbon::parse($record->expense_date)->toDateString()) }}" required>
                        @error('expense_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-paperclip mr-1 opacity-60"></i>Attachment
                        </label>
                        <input type="file" name="attachment" class="form-input w-full">
                        @if($record->attachment_url)
                            <p class="mt-1 text-xs text-slate-400"><a href="{{ $record->attachment_url }}" target="_blank" class="text-blue-600 underline">Current file</a> (upload new to replace)</p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-align-left mr-1 opacity-60"></i>Comment
                        </label>
                        <textarea name="comment" rows="2" class="form-input w-full" placeholder="Optional notes...">{{ old('comment', $record->comment) }}</textarea>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex items-center gap-1.5">
                        <i class="fas fa-save text-xs"></i> Update Expense
                    </button>
                    <a href="{{ route('department-expenses.index') }}" class="btn-secondary text-sm">Cancel</a>
                </div>
            </form>
        </article>
    </div>
</x-layouts.app>