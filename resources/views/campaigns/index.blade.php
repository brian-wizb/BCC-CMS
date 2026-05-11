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

        @if(session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <i class="fas fa-check-circle flex-shrink-0"></i> {{ session('success') }}
        </div>
        @endif

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
                        @forelse($campaigns as $i => $campaign)
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
                            <td class="px-4 py-3 text-slate-400">{{ $i + 1 }}</td>
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
                                    <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this campaign?')">
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
        </article>
    </div>
</x-layouts.app>
