<x-layouts.app title="Payroll Records">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(245,158,11,0.12);">
                    <i class="fas fa-money-check-alt text-base" style="color:rgba(245,158,11,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Payroll Records</h3>
                </div>
            </div>
            <a href="{{ route('payroll.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> Add Payroll
            </a>
        </div>

        @if(session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <i class="fas fa-check-circle flex-shrink-0"></i> {{ session('success') }}
        </div>
        @endif

        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3"><i class="fas fa-user mr-1.5 opacity-60"></i>Employee</th>
                            <th class="px-5 py-3"><i class="fas fa-briefcase mr-1.5 opacity-60"></i>Designation</th>
                            <th class="px-5 py-3"><i class="fas fa-credit-card mr-1.5 opacity-60"></i>Method</th>
                            <th class="px-5 py-3"><i class="fas fa-coins mr-1.5 opacity-60"></i>Gross (Tsh.)</th>
                            <th class="px-5 py-3"><i class="fas fa-hand-holding-usd mr-1.5 opacity-60"></i>Net (Tsh.)</th>
                            <th class="px-5 py-3">PAYE</th>
                            <th class="px-5 py-3"><i class="fas fa-calendar-alt mr-1.5 opacity-60"></i>Pay Date</th>
                            <th class="px-5 py-3"><i class="fas fa-paperclip mr-1.5 opacity-60"></i>Attach.</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($payrolls as $i => $payroll)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-[var(--color-ink-950)]">{{ $payroll->employee->name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $payroll->employee->designation ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $mc = match($payroll->method ?? '') {
                                        'Cash'   => 'bg-emerald-100 text-emerald-700',
                                        'Mobile' => 'bg-blue-100 text-blue-700',
                                        'Credit' => 'bg-purple-100 text-purple-700',
                                        'Cheque' => 'bg-amber-100 text-amber-700',
                                        'Bank'   => 'bg-sky-100 text-sky-700',
                                        default  => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $mc }}">{{ $payroll->method ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-[var(--color-ink-950)]">
                                {{ number_format(($payroll->salary ?? 0) + ($payroll->church_staffs_addition ?? 0) + ($payroll->other_amount ?? 0)) }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold" style="color:rgba(245,158,11,0.9);">{{ number_format($payroll->net_salary ?? 0) }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ number_format($payroll->paye ?? 0) }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-400">{{ \Carbon\Carbon::parse($payroll->payment_date)->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                @if($payroll->attachment_url)
                                    <a href="{{ $payroll->attachment_url }}" target="_blank" class="text-xs text-blue-600 underline">View</a>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('payroll.edit', $payroll) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('payroll.destroy', $payroll) }}" onsubmit="return confirm('Delete this payroll record?')">
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
                            <td colspan="10" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-money-check-alt mb-2 block text-2xl text-slate-300"></i>
                                No payroll records yet. <a href="{{ route('payroll.create') }}" class="text-blue-600 underline">Add the first one</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</x-layouts.app>