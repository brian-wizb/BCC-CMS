<x-layouts.app title="Expenses">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(239,68,68,0.12);">
                    <i class="fas fa-receipt text-base" style="color:rgba(239,68,68,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Expense Records</h3>
                </div>
            </div>
            <a href="{{ route('expenditures.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> New Expense
            </a>
        </div>

        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3"><i class="fas fa-tag mr-1.5 opacity-60"></i>Expense</th>
                            <th class="px-5 py-3"><i class="fas fa-credit-card mr-1.5 opacity-60"></i>Method</th>
                            <th class="px-5 py-3"><i class="fas fa-coins mr-1.5 opacity-60"></i>Amount (Tsh.)</th>
                            <th class="px-5 py-3"><i class="fas fa-calendar-alt mr-1.5 opacity-60"></i>Date</th>
                            <th class="px-5 py-3"><i class="fas fa-hashtag mr-1.5 opacity-60"></i>Reference</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3"><i class="fas fa-paperclip mr-1.5 opacity-60"></i>Attach.</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($expenditures as $i => $exp)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $expenditures->firstItem() + $i }}</td>
                            <td class="px-5 py-3.5 font-medium text-[var(--color-ink-950)]">{{ $exp->expense_category }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $mc = match($exp->payment_method) {
                                        'Cash'   => 'bg-emerald-100 text-emerald-700',
                                        'Mobile' => 'bg-blue-100 text-blue-700',
                                        'Credit' => 'bg-purple-100 text-purple-700',
                                        'Cheque' => 'bg-amber-100 text-amber-700',
                                        default  => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $mc }}">{{ $exp->payment_method }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-[var(--color-ink-950)]">{{ number_format($exp->amount) }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-400">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $exp->reference_no ?: '—' }}</td>
                            <td class="px-5 py-3.5">
                                @php $sc = ($exp->status ?? 'Paid') === 'Paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'; @endphp
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $sc }}">{{ $exp->status ?? 'Paid' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($exp->attachment_url)
                                    <a href="{{ $exp->attachment_url }}" target="_blank" class="text-xs text-blue-600 underline">View</a>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('expenditures.edit', $exp) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('expenditures.destroy', $exp) }}" onsubmit="return confirm('Delete this expense?')">
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
                            <td colspan="9" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-receipt mb-2 block text-2xl text-slate-300"></i>
                                No expense records found yet.
                                <a href="{{ route('expenditures.create') }}" class="ml-1 text-blue-600 underline">Add the first one</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($expenditures->hasPages())
            <div class="border-t border-[var(--color-surface-200)] px-5 py-4">
                {{ $expenditures->links() }}
            </div>
            @endif
        </article>
    </div>
</x-layouts.app>
