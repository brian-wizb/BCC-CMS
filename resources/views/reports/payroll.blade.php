<x-layouts.app title="Payroll Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(20,184,166,0.14);">
                    <i class="fas fa-money-bill-wave" style="color:rgba(20,184,166,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Payroll Report</h3>
                    <p class="text-xs text-slate-500">Staff salaries, net pay, PAYE, and payment breakdown</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.payroll.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
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

        <x-ui.date-range-filters :action="route('reports.payroll')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Gross Salary</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">TSh {{ number_format($totalSalary, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Net Salary</p>
                <p class="mt-1 text-2xl font-bold text-emerald-400">TSh {{ number_format($totalNetSalary, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total PAYE</p>
                <p class="mt-1 text-2xl font-bold text-rose-400">TSh {{ number_format($totalPaye, 2) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Paid Out</p>
                <p class="mt-1 text-2xl font-bold" style="color:rgba(20,184,166,0.9);">TSh {{ number_format($totalPaid, 2) }}</p>
                @php $outstanding = $totalNetSalary - $totalPaid; @endphp
                @if ($outstanding > 0)
                    <p class="mt-1 text-xs text-rose-400">TSh {{ number_format($outstanding, 2) }} outstanding</p>
                @endif
            </div>
        </article>

        {{-- By Employee --}}
        <article class="surface-card p-6">
            <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                <i class="fas fa-id-badge opacity-60"></i> Payroll by Employee
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 pb-3">Employee</th>
                            <th class="px-3 pb-3">Designation</th>
                            <th class="px-3 pb-3 text-right">Pay Periods</th>
                            <th class="px-3 pb-3 text-right">Total Gross</th>
                            <th class="px-3 pb-3 text-right">Total PAYE</th>
                            <th class="px-3 pb-3 text-right">Total Net</th>
                            <th class="px-3 pb-3 text-right">Total Paid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byEmployee as $row)
                            <tr>
                                <td class="px-3 py-3 font-medium text-[var(--color-ink-950)]">{{ $row->name }}</td>
                                <td class="px-3 py-3 text-slate-400">{{ $row->designation ?: '—' }}</td>
                                <td class="px-3 py-3 text-right">{{ $row->pay_periods }}</td>
                                <td class="px-3 py-3 text-right">{{ number_format($row->total_gross, 2) }}</td>
                                <td class="px-3 py-3 text-right text-rose-400">{{ number_format($row->total_paye, 2) }}</td>
                                <td class="px-3 py-3 text-right text-emerald-400">{{ number_format($row->total_net, 2) }}</td>
                                <td class="px-3 py-3 text-right" style="color:rgba(20,184,166,0.9);">{{ number_format($row->total_paid, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-8 text-center text-slate-400">No payroll records found.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($byEmployee->isNotEmpty())
                        <tfoot class="font-semibold text-[var(--color-ink-950)]">
                            <tr class="border-t border-[var(--color-surface-200)]">
                                <td class="px-3 pt-3" colspan="3">Total</td>
                                <td class="px-3 pt-3 text-right">{{ number_format($totalSalary, 2) }}</td>
                                <td class="px-3 pt-3 text-right text-rose-400">{{ number_format($totalPaye, 2) }}</td>
                                <td class="px-3 pt-3 text-right text-emerald-400">{{ number_format($totalNetSalary, 2) }}</td>
                                <td class="px-3 pt-3 text-right" style="color:rgba(20,184,166,0.9);">{{ number_format($totalPaid, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </article>

        {{-- Monthly Payroll Trend --}}
        <article class="surface-card p-6">
            <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                <i class="fas fa-chart-bar opacity-60"></i> Monthly Payroll Trend
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Month</th>
                            <th class="pb-3 text-right">Gross</th>
                            <th class="pb-3 text-right">Net</th>
                            <th class="pb-3 w-1/3">Distribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @php $maxGross = $monthlyPayroll->max('total_gross') ?: 1; @endphp
                        @forelse ($monthlyPayroll as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->month }}</td>
                                <td class="py-2.5 text-right">{{ number_format($row->total_gross, 2) }}</td>
                                <td class="py-2.5 text-right text-emerald-400">{{ number_format($row->total_net, 2) }}</td>
                                <td class="py-2.5">
                                    <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-200)]">
                                        <div class="h-full rounded-full" style="width:{{ round($row->total_gross / $maxGross * 100) }}%;background:linear-gradient(90deg,rgba(20,184,166,0.7),rgba(52,211,153,0.7));"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-slate-400">No trend data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        {{-- Recent Payroll Records --}}
        @if ($recentPayrolls->isNotEmpty())
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-history opacity-60"></i> Recent Payroll Records
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-3 pb-3">Employee</th>
                                <th class="px-3 pb-3">Designation</th>
                                <th class="px-3 pb-3 text-right">Gross</th>
                                <th class="px-3 pb-3 text-right">PAYE</th>
                                <th class="px-3 pb-3 text-right">Net</th>
                                <th class="px-3 pb-3 text-right">Paid</th>
                                <th class="px-3 pb-3">Payment Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @foreach ($recentPayrolls as $pr)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-[var(--color-ink-950)]">{{ $pr->employee?->name ?? '—' }}</td>
                                    <td class="px-3 py-3 text-slate-400">{{ $pr->employee?->designation ?: '—' }}</td>
                                    <td class="px-3 py-3 text-right">{{ number_format($pr->salary, 2) }}</td>
                                    <td class="px-3 py-3 text-right text-rose-400">{{ number_format($pr->paye, 2) }}</td>
                                    <td class="px-3 py-3 text-right text-emerald-400">{{ number_format($pr->net_salary, 2) }}</td>
                                    <td class="px-3 py-3 text-right" style="color:rgba(20,184,166,0.9);">{{ number_format($pr->paid_amount, 2) }}</td>
                                    <td class="px-3 py-3 text-slate-400">{{ optional($pr->payment_date)->format('d M Y') ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @endif

    </section>
</x-layouts.app>
