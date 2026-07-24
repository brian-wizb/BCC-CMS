<x-layouts.app title="Dashboard">
    <div class="dashboard-responsive">
    <section class="dashboard-hero">
        <div class="dashboard-hero-orb dashboard-hero-orb-a"></div>
        <div class="dashboard-hero-orb dashboard-hero-orb-b"></div>
        <div class="dashboard-hero-orb dashboard-hero-orb-c"></div>
        <div class="dashboard-hero-orb dashboard-hero-orb-d"></div>
        <div class="absolute inset-0 opacity-24 [background-image:repeating-linear-gradient(90deg,rgba(255,255,255,0.18)_0_1px,transparent_1px_24px)]"></div>
        <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="status-pill dashboard-hero-kicker">Leadership Overview</span>
                <h1 class="dashboard-hero-title mt-3 text-3xl font-bold md:text-4xl">Welcome to BCC Operations Center</h1>
                <p class="dashboard-hero-copy mt-2 max-w-3xl text-sm md:text-base">
                    See church growth, ministry activity, financial stewardship, and priorities that need attention—all in one clear view.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 dashboard-hero-stats-grid">
                <div class="dashboard-hero-stat">
                    <div class="dashboard-hero-stat-label">Population Monitored</div>
                    <div class="text-xl font-bold">{{ number_format($stats['members']) }}</div>
                </div>
                <div class="dashboard-hero-stat">
                    <div class="dashboard-hero-stat-label">Running Campaigns</div>
                    <div class="text-xl font-bold">{{ number_format($stats['running_campaigns']) }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 xl:grid-cols-4 md:grid-cols-2">
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Church Members</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($stats['members']) }}</p><p class="mt-1 text-xs text-slate-500">People under active discipleship</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Total Zones</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($stats['zones']) }}</p><p class="mt-1 text-xs text-slate-500">Community outreach coverage</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Running Campaigns</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($stats['running_campaigns']) }}</p><p class="mt-1 text-xs text-slate-500">Ongoing ministry initiatives</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Unfinished Pledges</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">Tsh {{ number_format($stats['unfinished_pledges_total'], 2) }}</p><p class="mt-1 text-xs text-slate-500">Pending commitment follow-through</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Discipleship Enrolled</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($stats['discipleship_enrolled']) }}</p><p class="mt-1 text-xs text-slate-500">Registered and external participants</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Foundation Completed</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($stats['discipleship_completed']) }}</p><p class="mt-1 text-xs text-slate-500">Completed all four stages</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Certificates Awarded</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($stats['discipleship_awarded']) }}</p><p class="mt-1 text-xs text-slate-500">Discipleship graduates</p></article>
    </section>

    {{-- ── Alerts Banner (only shown when there are open alerts) ────────── --}}
    @if($openAlertsCount > 0)
    <section class="mt-6">
        <a href="{{ route('alerts.index') }}" class="flex items-center justify-between gap-4 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 hover:bg-rose-100 transition-colors group">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-white shadow">
                    <i class="fa-solid fa-bell text-base"></i>
                </span>
                <div>
                    <p class="font-semibold text-rose-800">{{ $openAlertsCount }} Open Alert{{ $openAlertsCount !== 1 ? 's' : '' }} Require Attention</p>
                    <p class="text-xs text-rose-600">
                        @if($criticalAlertsCount > 0)
                            {{ $criticalAlertsCount }} critical &mdash;
                        @endif
                        Click to review and take action.
                    </p>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right text-rose-400 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </section>
    @endif

    <section class="mt-6 grid gap-4 xl:grid-cols-3 md:grid-cols-2">
        <article class="surface-card p-5"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Givings Total</p><p class="mt-2 text-2xl font-semibold">Tsh {{ number_format($stats['donations_total'], 2) }}</p></article>
        <article class="surface-card p-5"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Income Total</p><p class="mt-2 text-2xl font-semibold">Tsh {{ number_format($stats['income_total'], 2) }}</p></article>
        <article class="surface-card p-5"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Expenses Total</p><p class="mt-2 text-2xl font-semibold">Tsh {{ number_format($stats['expenditures_total'], 2) }}</p></article>
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
                <span class="status-pill">Inclusion View</span>
            </div>
            <div class="mx-auto w-[70%] max-w-[320px] dashboard-chart-wrap">
                <canvas id="genderPieChart" height="182"></canvas>
            </div>
        </article>
    </section>

    <section class="mt-6 grid gap-4 xl:grid-cols-[1.2fr_1fr]">
        <article class="surface-card dashboard-wood-panel p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold">Church Timetable</h2>
                <span class="status-pill">Weekly Rhythm</span>
            </div>

            <div class="space-y-2.5">
                @foreach ($timetable as $item)
                    <div class="dashboard-wood-row flex flex-col gap-2 px-4 py-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="text-xs uppercase tracking-[0.11em] text-slate-500">{{ $item['day'] }}</div>
                            <div class="text-[15px] font-semibold text-[var(--color-surface-900)]">{{ $item['session'] }}</div>
                        </div>
                        <div class="dashboard-wood-chip rounded-xl px-3 py-1.5 text-sm font-semibold text-[var(--color-surface-900)]">
                            {{ $item['time'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="surface-card dashboard-wood-panel p-5">
            <h2 class="text-xl font-semibold">Mission Focus</h2>
            <p class="mt-1 text-sm text-slate-500">
                Align operations around measurable ministry outcomes and faithful stewardship.
            </p>

            <div class="mt-4 space-y-3">
                @foreach ([
                    'Strengthen follow-up cycles for new believers',
                    'Improve giving transparency and pledge completion',
                    'Drive cross-ministry participation via zone coordination',
                    'Measure and report pastoral care responsiveness',
                ] as $focus)
                    <div class="dashboard-wood-row px-3 py-3 text-sm text-[var(--color-surface-900)]">
                        {{ $focus }}
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="mt-6 grid gap-4 xl:grid-cols-2">
        <article class="surface-card p-6">
            <h3 class="mb-4 text-lg font-semibold">Finance Trend</h3>
            <canvas id="financeBarChart" height="210"></canvas>
        </article>
        <article class="surface-card p-6">
            <h3 class="mb-4 text-lg font-semibold">Operational Coverage</h3>
            <canvas id="opsRadarChart" height="210"></canvas>
        </article>
    </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Chart) return;

        const ageData = @json($chartData['age']);
        const genderData = @json($chartData['gender']);
        const financeData = @json($chartData['finance']);
        const chartIds = ['agePieChart', 'genderPieChart', 'financeBarChart', 'opsRadarChart'];
        let chartInstances = [];

        const destroyCharts = () => {
            chartInstances.forEach((chart) => chart.destroy());
            chartInstances = [];
        };

        // ── Chart colour system ──────────────────────────────────────────
        // Independent from branding. Uses a proven data-viz palette
        // (Tableau-10 inspired) that is clearly distinguishable,
        // semantically meaningful, and colorblind-accessible.
        const VIZ = {
            // Qualitative — clearly distinct across any background
            blue:    '#4e79a7',
            orange:  '#f28e2b',
            red:     '#e15759',
            teal:    '#76b7b2',
            green:   '#59a14f',
            yellow:  '#edc948',
            purple:  '#b07aa1',
            pink:    '#ff9da7',
            brown:   '#9c755f',
            gray:    '#bab0ac',

            // Semantic aliases used in specific charts
            income:    '#59a14f',   // green  — money in  (positive)
            givings: '#4e79a7',     // blue   — gifts
            expenses:  '#e15759',   // red    — money out (alert)
            pledges:   '#f28e2b',   // orange — pending

            maleFill:   '#4e79a7',  // blue
            femaleFill: '#e05c8a',  // rose-pink
            otherFill:  '#76b7b2',  // teal

            // Structural
            cardBorder: 'rgba(255,255,255,0.055)',
        };

        // Age-group palette: 5 distinct, well-separated hues in reading order
        const AGE_COLORS = [VIZ.blue, VIZ.green, VIZ.orange, VIZ.purple, VIZ.red];
        // Gender palette
        const GENDER_COLORS = [VIZ.maleFill, VIZ.femaleFill, VIZ.otherFill];
        // Finance bar: semantically coloured per category
        const FINANCE_COLORS = [VIZ.givings, VIZ.income, VIZ.expenses, VIZ.pledges];
        // Radar accent
        const RADAR_BORDER  = VIZ.teal;
        const RADAR_FILL    = 'rgba(118,183,178,0.18)';
        const RADAR_POINT   = VIZ.orange;

        const getTheme = () => {
            const t = getComputedStyle(document.documentElement);
            return {
                text: t.getPropertyValue('--color-ink-950').trim() || '#e8f0ff',
                grid: 'rgba(255,255,255,0.08)',
                border: 'rgba(255,255,255,0.10)',
            };
        };

        const legendOpts = (textColor) => ({
            position: 'bottom',
            labels: {
                color: textColor,
                usePointStyle: true,
                pointStyle: 'circle',
                boxWidth: 10,
                boxHeight: 10,
                padding: 16,
            },
        });

        const renderCharts = () => {
            destroyCharts();

            const th = getTheme();
            const [ageCanvas, genderCanvas, financeCanvas, radarCanvas] = chartIds.map((id) => document.getElementById(id));

            if (!ageCanvas || !genderCanvas || !financeCanvas || !radarCanvas) return;

            // ── 1. Age group — Pie ───────────────────────────────────────────
            chartInstances.push(new Chart(ageCanvas.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ageData.map((d) => d.name),
                    datasets: [{
                        data: ageData.map((d) => Number(d.value || 0)),
                        backgroundColor: AGE_COLORS,
                        borderColor: th.border,
                        borderWidth: 2,
                        hoverOffset: 6,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: legendOpts(th.text),
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.label}: ${ctx.parsed} members`,
                            },
                        },
                    },
                },
            }));

            // ── 2. Gender — Doughnut ─────────────────────────────────────────
            chartInstances.push(new Chart(genderCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: genderData.map((d) => d.name),
                    datasets: [{
                        data: genderData.map((d) => Number(d.value || 0)),
                        backgroundColor: GENDER_COLORS,
                        borderColor: th.border,
                        borderWidth: 2,
                        hoverOffset: 6,
                    }],
                },
                options: {
                    responsive: true,
                    cutout: '62%',
                    plugins: {
                        legend: legendOpts(th.text),
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.label}: ${ctx.parsed} members`,
                            },
                        },
                    },
                },
            }));

            // ── 3. Finance — Bar (semantically coloured per category) ────────
            chartInstances.push(new Chart(financeCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: financeData.map((d) => d.name),
                    datasets: [{
                        label: 'Amount (Tsh)',
                        data: financeData.map((d) => Number(d.value || 0)),
                        backgroundColor: FINANCE_COLORS,
                        borderRadius: 8,
                        borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` Tsh ${Number(ctx.parsed.y).toLocaleString()}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: th.text, font: { size: 12 } },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: th.grid },
                            ticks: {
                                color: th.text,
                                font: { size: 11 },
                                callback: (v) => 'Tsh ' + (v >= 1000000
                                    ? (v / 1000000).toFixed(1) + 'M'
                                    : v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v),
                            },
                        },
                    },
                },
            }));

            // ── 4. Operational Coverage — Radar ──────────────────────────────
            chartInstances.push(new Chart(radarCanvas.getContext('2d'), {
                type: 'radar',
                data: {
                    labels: ['Members', 'Departments', 'Zones', 'Campaigns'],
                    datasets: [{
                        label: 'Coverage',
                        data: [
                            {{ $stats['members'] }},
                            {{ $stats['departments'] }},
                            {{ $stats['zones'] }},
                            {{ $stats['campaigns'] }},
                        ],
                        borderColor: RADAR_BORDER,
                        backgroundColor: RADAR_FILL,
                        pointBackgroundColor: RADAR_POINT,
                        pointBorderColor: th.border,
                        pointRadius: 4,
                        borderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        r: {
                            angleLines: { color: th.grid },
                            grid: { color: th.grid },
                            pointLabels: { color: th.text, font: { size: 11 } },
                            ticks: { display: false },
                        },
                    },
                },
            }));
        };

        renderCharts();
        window.addEventListener('bcc-theme-change', renderCharts);
    });
    </script>
    @endpush
    </div>
</x-layouts.app>
