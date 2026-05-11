<x-layouts.app title="Income Records">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(16,185,129,0.12);">
                    <i class="fas fa-hand-holding-usd text-base" style="color:rgba(16,185,129,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Income Records</h3>
                </div>
            </div>
            <a href="{{ route('income.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> Add Income
            </a>
        </div>

        {{-- Search --}}
        <div class="flex gap-2">
            <form method="GET" action="{{ route('income.index') }}" class="flex flex-1 gap-2 lg:max-w-sm">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input name="search" class="form-input w-full pl-8" value="{{ $search ?? '' }}" placeholder="Search by type or contributor...">
                </div>
                <button type="submit" class="btn-secondary flex items-center gap-1.5">Search</button>
            </form>
            @if(!empty($search))
                <a href="{{ route('income.index') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-times text-xs"></i> Clear
                </a>
            @endif
        </div>

        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3"><i class="fas fa-bookmark mr-1.5 opacity-60"></i>Income Type</th>
                            <th class="px-5 py-3"><i class="fas fa-coins mr-1.5 opacity-60"></i>Amount (Tsh.)</th>
                            <th class="px-5 py-3"><i class="fas fa-calendar-alt mr-1.5 opacity-60"></i>Received Date</th>
                            <th class="px-5 py-3"><i class="fas fa-user mr-1.5 opacity-60"></i>Contributor</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3"><i class="fas fa-paperclip mr-1.5 opacity-60"></i>Attach.</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($records as $income)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold" style="background:rgba(16,185,129,0.1); color:rgba(16,185,129,0.9);">
                                    {{ $income->incomeType->type ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-[var(--color-ink-950)]">{{ number_format($income->amount) }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-400">{{ \Carbon\Carbon::parse($income->received_date)->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                @if($income->contributor_name)
                                    <span class="font-medium text-[var(--color-ink-950)]">{{ $income->contributor_name }}</span>
                                    @if($income->contributor_contacts)
                                        <span class="block text-xs text-slate-400"><i class="fas fa-phone mr-1"></i>{{ $income->contributor_contacts }}</span>
                                    @endif
                                @elseif($income->member)
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-user-circle text-blue-400"></i>
                                        {{ $income->member->full_name ?? $income->member->name ?? '—' }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @php $sc = ($income->status ?? 'Received') === 'Received' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'; @endphp
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $sc }}">{{ $income->status ?? 'Received' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($income->attachment_url)
                                    <a href="{{ $income->attachment_url }}" target="_blank" class="text-xs text-blue-600 underline">View</a>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('income.edit', $income) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('income.destroy', $income) }}" onsubmit="return confirm('Delete this income record?')">
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
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-hand-holding-usd mb-2 block text-2xl text-slate-300"></i>
                                No income records found yet.
                                <a href="{{ route('income.create') }}" class="ml-1 text-blue-600 underline">Add the first one</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($records, 'hasPages') && $records->hasPages())
            <div class="border-t border-[var(--color-surface-200)] px-5 py-4">
                {{ $records->links() }}
            </div>
            @endif
        </article>
    </div>
</x-layouts.app>