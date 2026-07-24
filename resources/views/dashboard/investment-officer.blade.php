<x-layouts.app title="Investment Dashboard">
    <div class="dashboard-responsive">
        <section class="dashboard-hero">
            <div class="dashboard-hero-orb dashboard-hero-orb-a"></div>
            <div class="dashboard-hero-orb dashboard-hero-orb-b"></div>
            <div class="dashboard-hero-orb dashboard-hero-orb-c"></div>
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <span class="status-pill dashboard-hero-kicker">Income Management</span>
                    <h1 class="dashboard-hero-title mt-3 text-3xl font-bold md:text-4xl">Investment Officer Dashboard</h1>
                    <p class="dashboard-hero-copy mt-2 max-w-3xl text-sm md:text-base">
                        Monitor church income, maintain income classifications, and review member and tithe records.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 dashboard-hero-stats-grid">
                    <div class="dashboard-hero-stat">
                        <div class="dashboard-hero-stat-label">Income This Month</div>
                        <div class="text-xl font-bold">TZS {{ number_format($incomeStats['this_month']) }}</div>
                    </div>
                    <div class="dashboard-hero-stat">
                        <div class="dashboard-hero-stat-label">Income Types</div>
                        <div class="text-xl font-bold">{{ number_format($incomeStats['income_types']) }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="stat-card">
                <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Total Income</p>
                <p class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">TZS {{ number_format($incomeStats['total']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($incomeStats['income_records']) }} income records</p>
            </article>
            <article class="stat-card">
                <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Tithes Recorded</p>
                <p class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">TZS {{ number_format($incomeStats['tithes_total']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Read-only tithe visibility</p>
            </article>
            <article class="stat-card">
                <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Income Types</p>
                <p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($incomeStats['income_types']) }}</p>
                <a href="{{ route('income-types.index') }}" class="mt-2 inline-block text-xs text-[var(--color-brand-600)] hover:underline">Manage types</a>
            </article>
            <article class="stat-card">
                <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Members</p>
                <p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($incomeStats['members']) }}</p>
                <a href="{{ route('members.index') }}" class="mt-2 inline-block text-xs text-[var(--color-brand-600)] hover:underline">View members</a>
            </article>
        </section>

        <section class="mt-6 grid gap-4 xl:grid-cols-3">
            <article class="surface-card p-5 xl:col-span-2">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-semibold text-[var(--color-ink-950)]">Recent Income Records</h2>
                        <p class="text-xs text-slate-500">Latest income received across all classifications.</p>
                    </div>
                    <a href="{{ route('income.create') }}" class="btn-primary"><i class="fas fa-plus mr-1 text-xs"></i>Add Income</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-[var(--color-surface-200)] text-left text-xs uppercase tracking-wide text-slate-400">
                            <tr><th class="py-2">Type</th><th class="py-2">Contributor</th><th class="py-2">Date</th><th class="py-2 text-right">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @forelse($recentIncome as $income)
                                <tr>
                                    <td class="py-3 font-medium">{{ $income->incomeType?->type ?? 'Unclassified' }}</td>
                                    <td class="py-3 text-slate-500">{{ $income->contributor_name ?: ($income->member?->full_name ?? 'Anonymous') }}</td>
                                    <td class="py-3 text-slate-500">{{ \Carbon\Carbon::parse($income->received_date)->format('d M Y') }}</td>
                                    <td class="py-3 text-right font-semibold">TZS {{ number_format($income->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-slate-400">No income records yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('income.index') }}" class="mt-4 inline-block text-xs text-[var(--color-brand-600)] hover:underline">View all income records</a>
            </article>

            <article class="surface-card p-5">
                <h2 class="text-lg font-semibold text-[var(--color-ink-950)]">Income by Type</h2>
                <p class="mb-4 text-xs text-slate-500">Top classifications by total value.</p>
                <div class="space-y-3">
                    @forelse($incomeByType as $type)
                        <div class="rounded-xl border border-[var(--color-surface-200)] p-3">
                            <p class="text-sm font-semibold">{{ $type['name'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">TZS {{ number_format($type['value']) }}</p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-400">No income totals available.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="mt-6 surface-card p-5">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[var(--color-ink-950)]">Recent Tithe Records</h2>
                    <p class="text-xs text-slate-500">Read-only overview of recently recorded tithes.</p>
                </div>
                <a href="{{ route('givings.index') }}" class="text-xs text-[var(--color-brand-600)] hover:underline">View all tithes</a>
            </div>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @forelse($recentTithes as $tithe)
                    <div class="rounded-xl border border-[var(--color-surface-200)] p-3">
                        <p class="font-semibold text-[var(--color-ink-950)]">{{ $tithe->member?->full_name ?? $tithe->donor_name ?? 'Anonymous' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $tithe->tithe_code ?: 'No tithe code' }} · {{ $tithe->donation_date?->format('d M Y') }}</p>
                        <p class="mt-2 text-sm font-semibold">TZS {{ number_format($tithe->amount) }}</p>
                    </div>
                @empty
                    <p class="py-6 text-sm text-slate-400">No tithe records yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
