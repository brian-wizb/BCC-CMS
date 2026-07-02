<x-layouts.app title="Pledge Payments">
    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(16,185,129,0.12);">
                    <i class="fas fa-money-bill-wave text-base" style="color:rgba(16,185,129,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Pledge Payments</h3>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pledge-payments.export', request()->only(['search', 'date_from', 'date_to'])) }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <button type="button" onclick="window.print()" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-print text-xs"></i> Print
                </button>
                <a href="{{ route('pledge-payments.create') }}" class="btn-primary flex items-center gap-1.5">
                    <i class="fas fa-plus text-xs"></i> New Payment
                </a>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('pledge-payments.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative min-w-[180px] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input name="search" class="form-input w-full pl-8" value="{{ $search ?? '' }}" placeholder="Pledger name or phone...">
            </div>
            <button type="submit" class="btn-secondary">Search</button>
            @if(!empty($search))
                <a href="{{ route('pledge-payments.index') }}" class="btn-secondary flex items-center gap-1"><i class="fas fa-times text-xs"></i></a>
            @endif
            <div class="ml-auto flex items-center gap-2 text-sm text-slate-500">
                <span class="whitespace-nowrap">Show</span>
                <select name="per_page" onchange="this.form.submit()" class="form-input py-1.5 text-sm w-auto">
                    @foreach([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" @selected(($perPage ?? 20) == $n)>{{ $n }}</option>
                    @endforeach
                </select>
                <span>entries</span>
            </div>
        </form>

        <x-ui.date-range-filters :action="route('pledge-payments.index')" :date-from="$dateFrom" :date-to="$dateTo" />

        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-user mr-1.5 opacity-60"></i>Pledger</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-bullhorn mr-1.5 opacity-60"></i>Campaign</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-phone mr-1.5 opacity-60"></i>Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-file-invoice mr-1.5 opacity-60"></i>Invoice #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-coins mr-1.5 opacity-60"></i>Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-credit-card mr-1.5 opacity-60"></i>Method</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-calendar-alt mr-1.5 opacity-60"></i>Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><i class="fas fa-paperclip mr-1.5 opacity-60"></i>Attachment</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($pledgePayments as $i => $pp)
                        @php
                            $methodColors = [
                                'Cash'   => 'bg-emerald-50 text-emerald-700',
                                'Mobile' => 'bg-blue-50 text-blue-700',
                                'Credit' => 'bg-violet-50 text-violet-700',
                                'Cheque' => 'bg-amber-50 text-amber-700',
                                'Bank'   => 'bg-slate-100 text-slate-700',
                            ];
                            $mc = $methodColors[$pp->method] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-400">{{ $pledgePayments->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $pp->pledge->pledger_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pp->pledge->campaign->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pp->phone ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pp->invoice_number ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ number_format($pp->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $mc }}">{{ $pp->method }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $pp->payment_date ? \Carbon\Carbon::parse($pp->payment_date)->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3">
                                @if($pp->attachment)
                                    <a href="{{ $pp->attachment }}" target="_blank" class="text-xs text-blue-600 hover:underline"><i class="fas fa-paperclip mr-1"></i>View</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('pledge-payments.edit', $pp) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('pledge-payments.destroy', $pp) }}" data-confirm="Delete this payment?">
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
                            <td colspan="10" class="px-4 py-12 text-center">
                                <i class="fas fa-money-bill-wave mb-3 block text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-400">No payments recorded yet. <a href="{{ route('pledge-payments.create') }}" class="text-blue-600 underline">Add the first one</a>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pledgePayments->hasPages())
            <div class="border-t border-[var(--color-surface-200)] px-5 py-4">
                {{ $pledgePayments->links() }}
            </div>
            @endif
        </article>
    </div>
</x-layouts.app>
