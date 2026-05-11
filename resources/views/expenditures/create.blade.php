<x-layouts.app title="New Expense">
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(239,68,68,0.12);">
                    <i class="fas fa-plus text-base" style="color:rgba(239,68,68,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">New Expense</h3>
                </div>
            </div>
            <a href="{{ route('expenditures.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> All Expenses
            </a>
        </div>

        <article class="surface-card p-6">
            <form action="{{ route('expenditures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <i class="fas fa-tag mr-1 opacity-60"></i>Expense Category
                    </label>
                    <input type="text" name="expense_category" class="form-input w-full"
                        value="{{ old('expense_category') }}" placeholder="e.g. Church General Expenses" required>
                    @error('expense_category')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-credit-card mr-1 opacity-60"></i>Payment Type
                        </label>
                        <select name="payment_method" class="form-input w-full" required>
                            @foreach($paymentMethods as $m)
                                <option value="{{ $m }}" @selected(old('payment_method', 'Cash') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-calendar-alt mr-1 opacity-60"></i>Date
                        </label>
                        <input type="date" name="expense_date" class="form-input w-full"
                            value="{{ old('expense_date', today()->toDateString()) }}" required>
                        @error('expense_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-coins mr-1 opacity-60"></i>Amount (Tsh.)
                        </label>
                        <input type="number" step="1" min="0" name="amount" class="form-input w-full"
                            value="{{ old('amount') }}" placeholder="0" required>
                        @error('amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-hashtag mr-1 opacity-60"></i>Reference No.
                        </label>
                        <input type="text" name="reference_no" class="form-input w-full"
                            value="{{ old('reference_no') }}" placeholder="Payment / transaction number">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-info-circle mr-1 opacity-60"></i>Status
                        </label>
                        <select name="status" class="form-input w-full">
                            <option value="Paid" @selected(old('status', 'Paid') === 'Paid')>Paid</option>
                            <option value="Pending" @selected(old('status') === 'Pending')>Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-paperclip mr-1 opacity-60"></i>Attachment (Bank slip, receipt…)
                        </label>
                        <input type="file" name="attachment" class="form-input w-full">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <i class="fas fa-align-left mr-1 opacity-60"></i>Details
                    </label>
                    <textarea name="comment" rows="3" class="form-input w-full" placeholder="Optional notes...">{{ old('comment') }}</textarea>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex items-center gap-1.5">
                        <i class="fas fa-save text-xs"></i> Save Expense
                    </button>
                    <a href="{{ route('expenditures.index') }}" class="btn-secondary text-sm">Cancel</a>
                </div>
            </form>
        </article>
    </div>
</x-layouts.app>