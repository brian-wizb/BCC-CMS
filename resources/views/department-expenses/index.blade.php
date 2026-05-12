<x-layouts.app title="Department Expenses">
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(249,115,22,0.12);">
                <i class="fas fa-receipt text-base" style="color:rgba(249,115,22,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Department Expenses</h3>
            </div>
        </div>
        <a href="{{ route('department-expenses.create') }}" class="btn-primary flex items-center gap-1.5">
            <i class="fas fa-plus text-xs"></i> Add Expense
        </a>
    </div>

    {{-- Filters --}}
    <article class="surface-card p-4">
        <form method="GET" action="{{ route('department-expenses.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[140px]">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-input w-full">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-input w-full">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Department</label>
                <select name="department" class="form-input w-full">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-input w-full" placeholder="Expense, reference...">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="submit" class="btn-primary flex items-center gap-1.5 text-sm">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('department-expenses.index') }}" class="btn-secondary text-sm">Clear</a>
                <div class="flex items-center gap-2 text-sm text-slate-500 pl-2 border-l border-[var(--color-surface-200)]">
                    <span class="whitespace-nowrap">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="form-input py-1.5 text-sm w-auto">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected(($perPage ?? 20) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span>entries</span>
                </div>
            </div>
        </form>
    </article>

    {{-- Total stat --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="surface-card flex items-center gap-4 p-4">
            <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl" style="background:rgba(249,115,22,0.12);">
                <i class="fas fa-file-invoice-dollar text-lg" style="color:rgba(249,115,22,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Expenses</p>
                <p class="text-xl font-bold" style="color:rgba(249,115,22,0.9);">Tsh. {{ number_format($total ?? 0) }}</p>
            </div>
        </div>
    </div>

    <article class="surface-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Expense</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Payment Method</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Amount (Tsh.)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Attachment</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse($records as $record)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 text-slate-400">{{ $records->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold" style="background:rgba(249,115,22,0.10); color:rgba(249,115,22,0.9);">
                                <i class="fas fa-building mr-1 text-[10px]"></i>{{ $record->department }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $record->expense }}</td>
                        <td class="px-4 py-3">
                            @php
                                $methodColors = ['Cash'=>'rgba(16,185,129,0.12)/rgba(16,185,129,0.9)','Mobile'=>'rgba(59,130,246,0.12)/rgba(59,130,246,0.9)','Credit'=>'rgba(139,92,246,0.12)/rgba(139,92,246,0.9)','Cheque'=>'rgba(245,158,11,0.12)/rgba(245,158,11,0.9)','Bank'=>'rgba(100,116,139,0.12)/rgba(100,116,139,0.9)'];
                                [$bg, $fg] = explode('/', $methodColors[$record->payment_method] ?? 'rgba(100,116,139,0.12)/rgba(100,116,139,0.9)');
                            @endphp
                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold" style="background:{{ $bg }}; color:{{ $fg }};">{{ $record->payment_method }}</span>
                        </td>
                        <td class="px-4 py-3 font-semibold" style="color:rgba(249,115,22,0.9);">{{ number_format($record->amount) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ \Carbon\Carbon::parse($record->expense_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            @if($record->attachment_url)
                                <a href="{{ $record->attachment_url }}" target="_blank" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                    <i class="fas fa-paperclip mr-1 text-[10px]"></i>View
                                </a>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('department-expenses.edit', $record) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                    <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('department-expenses.destroy', $record) }}" data-confirm="Delete this record?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                        <i class="fas fa-trash mr-1 text-[10px]"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <i class="fas fa-receipt mb-3 block text-3xl text-slate-300"></i>
                            <p class="text-sm text-slate-400">No expense records found. <a href="{{ route('department-expenses.create') }}" class="text-blue-600 underline">Add the first one</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
    @if($records->hasPages())
    <div class="surface-card px-5 py-4">
        {{ $records->links() }}
    </div>
    @endif
</div>
</x-layouts.app>
