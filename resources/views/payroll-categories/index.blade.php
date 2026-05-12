<x-layouts.app title="Payroll Categories">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(245,158,11,0.12);">
                    <i class="fas fa-list-alt text-base" style="color:rgba(245,158,11,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance &rsaquo; Payroll</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Payroll Categories</h3>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Left: Table (2/3) --}}
            <div class="lg:col-span-2">
                <article class="surface-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                            <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="px-5 py-3">#</th>
                                    <th class="px-5 py-3">Name</th>
                                    <th class="px-5 py-3">Type</th>
                                    <th class="px-5 py-3">Charge In</th>
                                    <th class="px-5 py-3">Charge</th>
                                    <th class="px-5 py-3">After PAYE</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                                @forelse($categories as $cat)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3.5 text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-3.5 font-medium text-[var(--color-ink-950)]">{{ $cat->name }}</td>
                                    <td class="px-5 py-3.5">
                                        @if($cat->type === 'Addition')
                                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">Addition</span>
                                        @else
                                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700">Deduction</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if(($cat->charge_in ?? '') === 'Percent')
                                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700">Percent</span>
                                        @else
                                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600">Amount</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold text-[var(--color-ink-950)]">
                                        @if(($cat->charge_in ?? '') === 'Percent')
                                            {{ $cat->charge }}%
                                        @else
                                            Tsh. {{ number_format($cat->charge) }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($cat->deduct_after_paye)
                                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700">Yes</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <form method="POST" action="{{ route('payroll-categories.destroy', $cat) }}" data-confirm="Delete this category?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                                <i class="fas fa-trash mr-1 text-[10px]"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                        <i class="fas fa-list-alt mb-2 block text-2xl text-slate-300"></i>
                                        No categories yet. Add the first one.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            {{-- Right: Add Form (1/3) --}}
            <div>
                <article class="surface-card p-5">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg" style="background:rgba(245,158,11,0.12);">
                            <i class="fas fa-plus text-xs" style="color:rgba(245,158,11,0.9);"></i>
                        </span>
                        <h4 class="text-sm font-semibold text-[var(--color-ink-950)]">Add Category</h4>
                    </div>
                    <form action="{{ route('payroll-categories.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" class="form-input w-full" value="{{ old('name') }}" placeholder="e.g. NSSF" required>
                            @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Type <span class="text-rose-500">*</span></label>
                            <select name="type" class="form-input w-full" required>
                                <option value="Addition" @selected(old('type') === 'Addition')>Addition</option>
                                <option value="Deduction" @selected(old('type', 'Deduction') === 'Deduction')>Deduction</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Charge In <span class="text-rose-500">*</span></label>
                            <select name="charge_in" class="form-input w-full" required>
                                <option value="Percent" @selected(old('charge_in') === 'Percent')>Percent (%)</option>
                                <option value="Amount" @selected(old('charge_in', 'Amount') === 'Amount')>Amount (Tsh.)</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Charge <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="charge" class="form-input w-full" value="{{ old('charge', 0) }}" required>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="dap" name="deduct_after_paye" value="1" class="h-4 w-4 rounded border-slate-300 text-amber-500"
                                @checked(old('deduct_after_paye'))>
                            <label for="dap" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Deduct After PAYE</label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Comment</label>
                            <textarea name="comment" rows="2" class="form-input w-full" placeholder="Optional...">{{ old('comment') }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full flex items-center justify-center gap-1.5">
                            <i class="fas fa-save text-xs"></i> Save Category
                        </button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</x-layouts.app>
