<x-layouts.app title="Edit Expense">
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(245,158,11,0.12);">
                    <i class="fas fa-pen text-base" style="color:rgba(245,158,11,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit Expense</h3>
                </div>
            </div>
            <a href="{{ route('expenditures.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> All Expenses
            </a>
        </div>

        <article class="surface-card p-6">
            <form action="{{ route('expenditures.update', $expenditure) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <i class="fas fa-tag mr-1 opacity-60"></i>Expense Category
                    </label>
                    <input type="text" name="expense_category" class="form-input w-full"
                        value="{{ old('expense_category', $expenditure->expense_category) }}" required>
                    @error('expense_category')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-credit-card mr-1 opacity-60"></i>Payment Type
                        </label>
                        <select name="payment_method" class="form-input w-full" required>
                            @foreach($paymentMethods as $m)
                                <option value="{{ $m }}" @selected(old('payment_method', $expenditure->payment_method) === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-calendar-alt mr-1 opacity-60"></i>Date
                        </label>
                        <input type="date" name="expense_date" class="form-input w-full"
                            value="{{ old('expense_date', $expenditure->expense_date) }}" required>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-coins mr-1 opacity-60"></i>Amount (Tsh.)
                        </label>
                        <input type="number" step="1" min="0" name="amount" class="form-input w-full"
                            value="{{ old('amount', $expenditure->amount) }}" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-hashtag mr-1 opacity-60"></i>Reference No.
                        </label>
                        <input type="text" name="reference_no" class="form-input w-full"
                            value="{{ old('reference_no', $expenditure->reference_no) }}">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-info-circle mr-1 opacity-60"></i>Status
                        </label>
                        <select name="status" class="form-input w-full">
                            <option value="Paid" @selected(old('status', $expenditure->status) === 'Paid')>Paid</option>
                            <option value="Pending" @selected(old('status', $expenditure->status) === 'Pending')>Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-paperclip mr-1 opacity-60"></i>Attachment
                        </label>
                        @if($expenditure->attachment_url)
                            <a href="{{ $expenditure->attachment_url }}" target="_blank" class="mb-1 block text-xs text-blue-600 underline">
                                <i class="fas fa-file mr-1"></i>View current attachment
                            </a>
                        @endif
                        <input type="file" name="attachment" class="form-input w-full">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <i class="fas fa-align-left mr-1 opacity-60"></i>Details
                    </label>
                    <textarea name="comment" rows="3" class="form-input w-full">{{ old('comment', $expenditure->comment) }}</textarea>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex items-center gap-1.5">
                        <i class="fas fa-save text-xs"></i> Update Expense
                    </button>
                    <a href="{{ route('expenditures.index') }}" class="btn-secondary text-sm">Cancel</a>
                </div>
            </form>
        </article>
    </div>
</x-layouts.app>