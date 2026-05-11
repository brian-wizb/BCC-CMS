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
            <a href="{{ route('pledge-payments.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> New Payment
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
                    <thead class="bg-[var(--color-surface-50)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Pledger</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Campaign</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Invoice #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Attachment</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Actions</th>
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
                            <td class="px-4 py-3 text-slate-400">{{ $i + 1 }}</td>
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
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('pledge-payments.edit', $pp) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('pledge-payments.destroy', $pp) }}" onsubmit="return confirm('Delete this payment?')">
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
        </article>
    </div>
</x-layouts.app>
