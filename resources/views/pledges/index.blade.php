<x-layouts.app title="Pledges">
    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(139,92,246,0.12);">
                    <i class="fas fa-handshake text-base" style="color:rgba(139,92,246,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Pledges</h3>
                </div>
            </div>
            <a href="{{ route('pledges.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> New Pledge
            </a>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('pledges.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative min-w-[180px] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input name="search" class="form-input w-full pl-8" value="{{ $search ?? '' }}" placeholder="Name, phone or campaign...">
            </div>
            <button type="submit" class="btn-secondary">Search</button>
            @if(!empty($search))
                <a href="{{ route('pledges.index') }}" class="btn-secondary flex items-center gap-1"><i class="fas fa-times text-xs"></i></a>
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

        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Campaign</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Paid Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Due Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Start Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Due/End Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($pledges as $i => $pledge)
                        <tr class="hover:bg-slate-50 transition">
                            @php $paid = $pledge->payments->sum('amount'); $due = max(0, $pledge->amount - $paid); @endphp
                            <td class="px-4 py-3 text-slate-400">{{ $pledges->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $pledge->pledger_name ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pledge->pledger_phone ?: '—' }}</td>
                            <td class="px-4 py-3">
                                @if($pledge->campaign)
                                    <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold" style="background:rgba(245,158,11,0.12); color:rgba(245,158,11,0.9);">{{ $pledge->campaign->name }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ number_format($pledge->amount, 2) }}</td>
                            <td class="px-4 py-3 text-emerald-700 font-medium">{{ number_format($paid, 2) }}</td>
                            <td class="px-4 py-3 {{ $due > 0 ? 'text-rose-600 font-semibold' : 'text-emerald-600' }}">{{ number_format($due, 2) }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pledge->due_date ? \Carbon\Carbon::parse($pledge->due_date)->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('pledges.edit', $pledge) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('pledges.destroy', $pledge) }}" data-confirm="Delete this pledge?">
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
                                <i class="fas fa-handshake mb-3 block text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-400">No pledges yet. <a href="{{ route('pledges.create') }}" class="text-blue-600 underline">Add the first one</a>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pledges->hasPages())
            <div class="border-t border-[var(--color-surface-200)] px-5 py-4">
                {{ $pledges->links() }}
            </div>
            @endif
        </article>
    </div>
</x-layouts.app>
