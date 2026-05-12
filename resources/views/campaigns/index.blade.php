<x-layouts.app title="Campaigns">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(245,158,11,0.12);">
                    <i class="fas fa-bullhorn text-base" style="color:rgba(245,158,11,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Campaigns</h3>
                </div>
            </div>
            <a href="{{ route('campaigns.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> New Campaign
            </a>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('campaigns.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative min-w-[180px] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input name="search" class="form-input w-full pl-8" value="{{ $search ?? '' }}" placeholder="Campaign name or description...">
            </div>
            <button type="submit" class="btn-secondary">Search</button>
            @if(!empty($search))
                <a href="{{ route('campaigns.index') }}" class="btn-secondary flex items-center gap-1"><i class="fas fa-times text-xs"></i></a>
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

        {{-- Table --}}
        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Target (Tsh.)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Start Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">End Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($campaigns as $campaign)
                        @php
                            $now = \Carbon\Carbon::today();
                            $start = $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date) : null;
                            $end   = $campaign->end_date   ? \Carbon\Carbon::parse($campaign->end_date)   : null;
                            if ($start && $now->lt($start)) {
                                $statusLabel = 'Upcoming'; $statusClass = 'bg-blue-50 text-blue-600';
                            } elseif ($end && $now->gt($end)) {
                                $statusLabel = 'Ended'; $statusClass = 'bg-slate-100 text-slate-500';
                            } else {
                                $statusLabel = 'Active'; $statusClass = 'bg-emerald-50 text-emerald-600';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-400">{{ $campaigns->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $campaign->name }}</td>
                            <td class="px-4 py-3 max-w-xs text-slate-500">
                                <span class="line-clamp-1" title="{{ $campaign->description }}">{{ $campaign->description ?: '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $campaign->target_amount ? number_format($campaign->target_amount) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('campaigns.edit', $campaign) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}" data-confirm="Delete this campaign?">
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
                                <i class="fas fa-bullhorn mb-3 block text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-400">No campaigns yet. <a href="{{ route('campaigns.create') }}" class="text-blue-600 underline">Create the first one</a>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($campaigns->hasPages())
            <div class="border-t border-[var(--color-surface-200)] px-5 py-4">
                {{ $campaigns->links() }}
            </div>
            @endif
        </article>
    </div>
</x-layouts.app>
