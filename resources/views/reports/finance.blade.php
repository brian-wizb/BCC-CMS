<x-layouts.app title="Finance Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-coins" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Finance Report</h3>
                    <p class="text-xs text-slate-500">Income, expenditure, donations, and department finances</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.finance.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <button onclick="window.print()" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-print text-xs"></i> Print
                </button>
                <a href="{{ route('reports.index') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs"></i> All reports
                </a>
            </div>
        </div>

        <x-ui.date-range-filters :action="route('reports.finance')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Summary stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Income</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">TSh {{ number_format($totalIncome + $totalDonations + $totalDeptIncome, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500">General + Donations + Dept</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Expenditure</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">TSh {{ number_format($totalExpenditure + $totalDeptExpense, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500">General + Dept expenses</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Net Surplus / Deficit</p>
                <p class="mt-1 text-2xl font-bold {{ $netSurplus >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    TSh {{ number_format(abs($netSurplus), 2) }}
                    <span class="text-sm font-normal">{{ $netSurplus >= 0 ? 'surplus' : 'deficit' }}</span>
                </p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">General Income</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">TSh {{ number_format($totalIncome, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Donations</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">TSh {{ number_format($totalDonations, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Dept Income / Expense</p>
                <p class="mt-1 text-lg font-bold text-[var(--color-ink-950)]">
                    <span class="text-emerald-400">{{ number_format($totalDeptIncome, 2) }}</span>
                    <span class="text-slate-500 text-sm">/</span>
                    <span class="text-rose-400">{{ number_format($totalDeptExpense, 2) }}</span>
                </p>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Income by type --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-arrow-circle-up opacity-60" style="color:rgba(52,211,153,0.8);"></i> Income by Type
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Type</th>
                            <th class="pb-3 text-right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($incomeByType as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->type }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-slate-400">No income records.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($incomeByType->isNotEmpty())
                        <tfoot>
                            <tr class="font-semibold text-[var(--color-ink-950)]">
                                <td class="pt-3 border-t border-[var(--color-surface-200)]">Total</td>
                                <td class="pt-3 text-right border-t border-[var(--color-surface-200)]">{{ number_format($totalIncome, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </article>

            {{-- Expenditure by category --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-arrow-circle-down opacity-60" style="color:rgba(255,111,145,0.8);"></i> Expenditure by Category
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Category</th>
                            <th class="pb-3 text-right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($expByCategory as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->expense_category ?: 'Uncategorized' }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-slate-400">No expenditure records.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($expByCategory->isNotEmpty())
                        <tfoot>
                            <tr class="font-semibold text-[var(--color-ink-950)]">
                                <td class="pt-3 border-t border-[var(--color-surface-200)]">Total</td>
                                <td class="pt-3 text-right border-t border-[var(--color-surface-200)]">{{ number_format($totalExpenditure, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </article>

            {{-- Donations by type --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-hand-holding-heart opacity-60" style="color:rgba(244,193,93,0.8);"></i> Donations by Type
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Type</th>
                            <th class="pb-3 text-right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($donByType as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucfirst($row->type ?: 'General') }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-slate-400">No donation records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            {{-- Department income vs expense --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-sitemap opacity-60"></i> Department Income vs Expense
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Department</th>
                                <th class="pb-3 text-right">Income</th>
                                <th class="pb-3 text-right">Expense</th>
                                <th class="pb-3 text-right">Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @php
                                $allDepts = $deptIncByDept->pluck('total', 'department')
                                    ->union($deptExpByDept->pluck('total', 'department'))
                                    ->keys()->unique()->sort();
                            @endphp
                            @forelse ($allDepts as $dept)
                                @php
                                    $inc = $deptIncByDept->firstWhere('department', $dept)?->total ?? 0;
                                    $exp = $deptExpByDept->firstWhere('department', $dept)?->total ?? 0;
                                    $net = $inc - $exp;
                                @endphp
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $dept ?: 'General' }}</td>
                                    <td class="py-2.5 text-right text-emerald-400">{{ number_format($inc, 2) }}</td>
                                    <td class="py-2.5 text-right text-rose-400">{{ number_format($exp, 2) }}</td>
                                    <td class="py-2.5 text-right font-semibold {{ $net >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ number_format($net, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-center text-slate-400">No department finance records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>

        {{-- Monthly income trend --}}
        @if ($monthlyIncome->isNotEmpty())
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-chart-bar opacity-60"></i> Monthly Income Trend
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Month</th>
                                <th class="pb-3 text-right">Income (KES)</th>
                                <th class="pb-3 w-2/5">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @php $maxIncome = $monthlyIncome->max('total') ?: 1; @endphp
                            @foreach ($monthlyIncome as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</td>
                                    <td class="py-2.5 text-right">{{ number_format($row->total, 2) }}</td>
                                    <td class="py-2.5">
                                        <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                            <div class="h-full rounded-full" style="width:{{ round($row->total / $maxIncome * 100) }}%;background:linear-gradient(90deg,rgba(52,211,153,0.8),rgba(36,184,255,0.8));"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @endif

    </section>
</x-layouts.app>
