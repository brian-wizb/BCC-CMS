<x-layouts.app title="Donation Records">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-[var(--color-ink-950)]">Donation Records</h1>
            <a href="{{ route('donations.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus mr-2"></i> New Donation
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

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
                                              onsubmit="return confirm('Delete this donation record? This cannot be undone.')">
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
                                <td colspan="10" class="px-5 py-12 text-center text-slate-400">
                                    No donations recorded yet.
                                    <a href="{{ route('donations.create') }}" class="ml-2 text-blue-600 underline">Add the first one</a>.
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
