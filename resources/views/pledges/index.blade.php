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
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Pledger Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Campaign</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Amount (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Notes</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($pledges as $i => $pledge)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $pledge->pledger_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pledge->pledger_email ?: '—' }}</td>
                            <td class="px-4 py-3">
                                @if($pledge->campaign)
                                    <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold" style="background:rgba(245,158,11,0.12); color:rgba(245,158,11,0.9);">{{ $pledge->campaign->name }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ number_format($pledge->amount, 2) }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3 max-w-xs text-slate-500">
                                <span class="line-clamp-1" title="{{ $pledge->notes }}">{{ $pledge->notes ?: '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('pledges.edit', $pledge) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('pledges.destroy', $pledge) }}" onsubmit="return confirm('Delete this pledge?')">
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
                                <i class="fas fa-handshake mb-3 block text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-400">No pledges yet. <a href="{{ route('pledges.create') }}" class="text-blue-600 underline">Add the first one</a>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</x-layouts.app>
                <td>{{ $pledge->pledger_name ?? $pledge->unregistered_name ?? '—' }}</td>
                <td>{{ $pledge->phone ?? $pledge->unregistered_phone ?? '—' }}</td>
                <td>{{ $pledge->pledge_type ?? '—' }}</td>
                <td>{{ $pledge->campaign->name ?? '' }}</td>
                <td>{{ number_format($pledge->amount, 2) }}</td>
                <td>{{ number_format($paid, 2) }}</td>
                <td class="{{ $due > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">{{ number_format($due, 2) }}</td>
                <td>{{ $pledge->start_date ? \Carbon\Carbon::parse($pledge->start_date)->toDateString() : '' }}</td>
                <td>{{ $pledge->due_date ? \Carbon\Carbon::parse($pledge->due_date)->toDateString() : '' }}</td>
                <td>
                    <a href="{{ route('pledges.edit', $pledge) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('pledges.destroy', $pledge) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this pledge?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
