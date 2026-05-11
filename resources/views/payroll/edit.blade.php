<x-layouts.app title="Edit Payroll">
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(245,158,11,0.12);">
                    <i class="fas fa-pen text-base" style="color:rgba(245,158,11,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance &rsaquo; Payroll</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit Payroll Entry</h3>
                </div>
            </div>
            <a href="{{ route('payroll.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> All Payrolls
            </a>
        </div>

        <article class="surface-card p-6">
            <form action="{{ route('payroll.update', $payroll) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')

                <div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-widest text-slate-400">Employee &amp; Payment</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-user mr-1 opacity-60"></i>Employee <span class="text-rose-500">*</span>
                            </label>
                            <select name="employee_id" class="form-input w-full" required>
                                <option value="">— Select employee —</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" @selected(old('employee_id', $payroll->employee_id) == $emp->id)>
                                        {{ $emp->name }}{{ $emp->designation ? ' – '.$emp->designation : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-calendar-alt mr-1 opacity-60"></i>Payment Date <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="payment_date" class="form-input w-full"
                                value="{{ old('payment_date', \Carbon\Carbon::parse($payroll->payment_date)->toDateString()) }}" required>
                            @error('payment_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-credit-card mr-1 opacity-60"></i>Payment Method
                            </label>
                            <select name="method" class="form-input w-full">
                                @foreach(['Cash','Mobile','Credit','Cheque','Bank'] as $m)
                                    <option value="{{ $m }}" @selected(old('method', $payroll->method) === $m)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-university mr-1 opacity-60"></i>Account Name
                            </label>
                            <input type="text" name="account_name" class="form-input w-full"
                                value="{{ old('account_name', $payroll->account_name) }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-hashtag mr-1 opacity-60"></i>Account Number
                            </label>
                            <input type="text" name="account_number" class="form-input w-full"
                                value="{{ old('account_number', $payroll->account_number) }}">
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-[var(--color-surface-200)] p-4">
                    <p class="mb-3 text-xs font-bold uppercase tracking-widest text-slate-400">Salary Breakdown</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-coins mr-1 opacity-60"></i>Gross Salary (Tsh.) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="salary" name="salary" step="1" min="0" class="form-input w-full"
                                value="{{ old('salary', $payroll->salary) }}" required oninput="calcNet()">
                            @error('salary')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-percentage mr-1 opacity-60"></i>Tax % (PAYE Rate)
                            </label>
                            <input type="number" id="tax_percent" name="tax_percent" step="0.01" min="0" max="100"
                                class="form-input w-full" value="{{ old('tax_percent', $payroll->tax_percent ?? 0) }}" oninput="calcNet()">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-plus-circle mr-1 opacity-60" style="color:rgba(16,185,129,0.8);"></i>Church Staff Addition (Tsh.)
                            </label>
                            <input type="number" id="church_staffs_addition" name="church_staffs_addition" step="1" min="0"
                                class="form-input w-full" value="{{ old('church_staffs_addition', $payroll->church_staffs_addition ?? 0) }}" oninput="calcNet()">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-minus-circle mr-1 opacity-60" style="color:rgba(239,68,68,0.8);"></i>PAYE Amount (Tsh.)
                            </label>
                            <input type="number" id="paye" name="paye" step="1" min="0"
                                class="form-input w-full" value="{{ old('paye', $payroll->paye ?? 0) }}" oninput="calcNet()">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-hand-holding-usd mr-1 opacity-60" style="color:rgba(245,158,11,0.9);"></i>Net Salary (Tsh.)
                            </label>
                            <input type="number" id="net_salary" name="net_salary" step="1" class="form-input w-full font-bold"
                                value="{{ old('net_salary', $payroll->net_salary ?? 0) }}" readonly
                                style="background:rgba(245,158,11,0.06); color:rgba(245,158,11,0.9);">
                            <p class="mt-1 text-xs text-slate-400">Auto-calculated: Salary + Addition &minus; PAYE</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-paperclip mr-1 opacity-60"></i>Attachment
                        </label>
                        <input type="file" name="attachment" class="form-input w-full">
                        @if($payroll->attachment_url)
                            <p class="mt-1 text-xs text-slate-400">
                                <a href="{{ $payroll->attachment_url }}" target="_blank" class="text-blue-600 underline">Current file</a> (upload new to replace)
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-align-left mr-1 opacity-60"></i>Details / Notes
                        </label>
                        <textarea name="details" rows="2" class="form-input w-full">{{ old('details', $payroll->details) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex items-center gap-1.5">
                        <i class="fas fa-save text-xs"></i> Update Payroll
                    </button>
                    <a href="{{ route('payroll.index') }}" class="btn-secondary text-sm">Cancel</a>
                </div>
            </form>
        </article>
    </div>

    <script>
    function calcNet() {
        var salary = parseFloat(document.getElementById('salary').value) || 0;
        var addition = parseFloat(document.getElementById('church_staffs_addition').value) || 0;
        var paye = parseFloat(document.getElementById('paye').value) || 0;
        var net = salary + addition - paye;
        document.getElementById('net_salary').value = net < 0 ? 0 : Math.round(net);
    }
    document.addEventListener('DOMContentLoaded', calcNet);
    </script>
</x-layouts.app>
