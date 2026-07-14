<x-layouts.app title="Secretary Dashboard">
    <div class="dashboard-responsive">
        <section class="dashboard-hero">
            <div class="dashboard-hero-orb dashboard-hero-orb-a"></div>
            <div class="dashboard-hero-orb dashboard-hero-orb-b"></div>
            <div class="dashboard-hero-orb dashboard-hero-orb-c"></div>
            <div class="dashboard-hero-orb dashboard-hero-orb-d"></div>
            <div class="absolute inset-0 opacity-24 [background-image:repeating-linear-gradient(90deg,rgba(255,255,255,0.18)_0_1px,transparent_1px_24px)]"></div>
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <span class="status-pill dashboard-hero-kicker">People Management</span>
                    <h1 class="dashboard-hero-title mt-3 text-3xl font-bold md:text-4xl">Secretary People Dashboard</h1>
                    <p class="dashboard-hero-copy mt-2 max-w-3xl text-sm md:text-base">
                        Track member growth, follow-up workload, and structure coverage across families, departments, zones, and groups.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 dashboard-hero-stats-grid">
                    <div class="dashboard-hero-stat">
                        <div class="dashboard-hero-stat-label">Members</div>
                        <div class="text-xl font-bold">{{ number_format($peopleStats['members']) }}</div>
                    </div>
                    <div class="dashboard-hero-stat">
                        <div class="dashboard-hero-stat-label">Pending Follow-up</div>
                        <div class="text-xl font-bold">{{ number_format($peopleStats['follow_up_pending']) }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-4 xl:grid-cols-4 md:grid-cols-2">
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Members</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['members']) }}</p><p class="mt-1 text-xs text-slate-500">Registered members</p></article>
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Families</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['families']) }}</p><p class="mt-1 text-xs text-slate-500">Households managed</p></article>
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Visitors</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['visitors']) }}</p><p class="mt-1 text-xs text-slate-500">Visitor records</p></article>
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Leaders</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['leaders']) }}</p><p class="mt-1 text-xs text-slate-500">Leader profiles</p></article>
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Departments</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['departments']) }}</p><p class="mt-1 text-xs text-slate-500">Department units</p></article>
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Zones</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['zones']) }}</p><p class="mt-1 text-xs text-slate-500">Pastoral zones</p></article>
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Groups</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['groups']) }}</p><p class="mt-1 text-xs text-slate-500">Discipleship groups</p></article>
            <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Follow-up Pending</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($peopleStats['follow_up_pending']) }}</p><p class="mt-1 text-xs text-slate-500">Pending or in-progress</p></article>
        </section>

        <section class="mt-6 grid gap-4 xl:grid-cols-2">
            <article class="surface-card p-6">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Age Group Dynamics</h3>
                    <span class="status-pill">Demographics</span>
                </div>
                <div class="mx-auto w-[70%] max-w-[320px] dashboard-chart-wrap">
                    <canvas id="agePieChart" height="182"></canvas>
                </div>
            </article>
            <article class="surface-card p-6">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Gender Distribution</h3>
                    <span class="status-pill">People Profile</span>
                </div>
                <div class="mx-auto w-[70%] max-w-[320px] dashboard-chart-wrap">
                    <canvas id="genderPieChart" height="182"></canvas>
                </div>
            </article>
        </section>

        <section class="mt-6 grid gap-4 xl:grid-cols-2">
            <article class="surface-card p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Recently Added Members</h3>
                    <a href="{{ route('members.index') }}" class="text-xs text-[var(--color-brand-600)] hover:underline">View all</a>
                </div>
                <ul class="divide-y divide-[var(--color-surface-200)]">
                    @forelse ($recentMembers as $member)
                        <li class="py-2.5 text-sm">
                            <p class="font-semibold text-[var(--color-ink-950)]">{{ $member->full_name ?: 'Unnamed member' }}</p>
                            <p class="text-xs text-slate-500">{{ $member->phone ?: 'No phone' }} • Added {{ $member->created_at?->format('d M Y') }}</p>
                        </li>
                    @empty
                        <li class="py-4 text-sm text-slate-400">No members yet.</li>
                    @endforelse
                </ul>
            </article>

            <article class="surface-card p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Recently Added Visitors</h3>
                    <a href="{{ route('visitors.index') }}" class="text-xs text-[var(--color-brand-600)] hover:underline">View all</a>
                </div>
                <ul class="divide-y divide-[var(--color-surface-200)]">
                    @forelse ($recentVisitors as $visitor)
                        <li class="py-2.5 text-sm">
                            <p class="font-semibold text-[var(--color-ink-950)]">{{ $visitor->full_name ?: 'Unnamed visitor' }}</p>
                            <p class="text-xs text-slate-500">{{ $visitor->phone ?: 'No phone' }} • First visit {{ $visitor->first_visit_date ? \Illuminate\Support\Carbon::parse($visitor->first_visit_date)->format('d M Y') : 'N/A' }}</p>
                        </li>
                    @empty
                        <li class="py-4 text-sm text-slate-400">No visitors yet.</li>
                    @endforelse
                </ul>
            </article>
        </section>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Chart) return;

            const ageData = @json($chartData['age']);
            const genderData = @json($chartData['gender']);

            const getTheme = () => {
                const t = getComputedStyle(document.documentElement);
                return {
                    text: t.getPropertyValue('--color-ink-950').trim() || '#e8f0ff',
                    border: 'rgba(255,255,255,0.10)',
                };
            };

            const th = getTheme();
            const legendOpts = {
                position: 'bottom',
                labels: {
                    color: th.text,
                    usePointStyle: true,
                    pointStyle: 'circle',
                    boxWidth: 10,
                    boxHeight: 10,
                    padding: 16,
                },
            };

            new Chart(document.getElementById('agePieChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ageData.map((d) => d.name),
                    datasets: [{
                        data: ageData.map((d) => Number(d.value || 0)),
                        backgroundColor: ['#4e79a7', '#59a14f', '#f28e2b', '#b07aa1'],
                        borderColor: th.border,
                        borderWidth: 2,
                    }],
                },
                options: { responsive: true, plugins: { legend: legendOpts } },
            });

            new Chart(document.getElementById('genderPieChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: genderData.map((d) => d.name),
                    datasets: [{
                        data: genderData.map((d) => Number(d.value || 0)),
                        backgroundColor: ['#4e79a7', '#e05c8a', '#76b7b2'],
                        borderColor: th.border,
                        borderWidth: 2,
                    }],
                },
                options: { responsive: true, cutout: '62%', plugins: { legend: legendOpts } },
            });
        });
        </script>
        @endpush
    </div>
</x-layouts.app>
