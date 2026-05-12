<x-layouts.app title="Donation Records">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(16,185,129,0.12);">
                    <i class="fas fa-hand-holding-heart text-base" style="color:rgba(16,185,129,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Donation Records</h3>
                </div>
            </div>
            <a href="{{ route('donations.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> New Donation
            </a>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('donations.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative min-w-[180px] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input name="search" class="form-input w-full pl-8" value="{{ $search ?? '' }}" placeholder="Name, type, tithe code, reference...">
            </div>
            <button type="submit" class="btn-secondary">Search</button>
            @if(!empty($search))
                <a href="{{ route('donations.index') }}" class="btn-secondary flex items-center gap-1"><i class="fas fa-times text-xs"></i></a>
            @endif
            <div class="ml-auto flex items-center gap-2 text-sm text-slate-500">
                <span class="whitespace-nowrap">Show</span>
                <select name="per_page" onchange="this.form.submit()" class="form-input py-1.5 text-sm w-auto">
                    @foreach([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" @selected(($perPage ?? 25) == $n)>{{ $n }}</option>
                    @endforeach
                </select>
                <span>entries</span>
            </div>
        </form>

        {{-- Table --}}
        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Member</th>
                            <th class="px-5 py-3">Tithe Code</th>
                            <th class="px-5 py-3">Donation Type</th>
                            <th class="px-5 py-3">Amount (TZS)</th>
                            <th class="px-5 py-3">Method</th>
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Attachment</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse ($donations as $donation)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 text-slate-400">{{ $donations->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3 font-medium text-[var(--color-ink-950)]">
                                    {{ $donation->member?->full_name ?? $donation->donor_name ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-slate-500">{{ $donation->tithe_code ?: '—' }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $typeClasses = match($donation->type) {
                                            'Tithe [Zaka]'       => 'bg-emerald-100 text-emerald-700',
                                            'Sadaka ya Shukrani' => 'bg-blue-100 text-blue-700',
                                            'Mission'            => 'bg-purple-100 text-purple-700',
                                            default              => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $typeClasses }}">
                                        {{ $donation->type ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-medium text-[var(--color-ink-950)]">{{ number_format($donation->amount, 2) }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $donation->method ?: '—' }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $donation->reference ?: '—' }}</td>
                                <td class="px-5 py-3 text-slate-400 whitespace-nowrap">{{ $donation->donation_date->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    @if ($donation->attachment)
                                        <a href="{{ Storage::url($donation->attachment) }}" target="_blank"
                                           class="text-xs text-blue-600 underline">
                                            View
                                        </a>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('donations.edit', $donation) }}"
                                           class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('donations.destroy', $donation) }}"
                                              data-confirm="Delete this donation record? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-12 text-center">
                                    <i class="fas fa-hand-holding-heart mb-3 block text-3xl text-slate-300"></i>
                                    <p class="text-sm text-slate-400">No donations recorded yet. <a href="{{ route('donations.create') }}" class="text-blue-600 underline">Add the first one</a>.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($donations->hasPages())
                <div class="border-t border-[var(--color-surface-200)] px-5 py-3">
                    {{ $donations->links() }}
                </div>
            @endif
        </article>
    </div>

</x-layouts.app>
