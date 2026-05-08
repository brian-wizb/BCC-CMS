<x-layouts.app title="Attendance Reports">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('attendance.index') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-500 text-white shadow">
                <i class="fa-solid fa-chart-bar text-xl"></i>
            </span>
            <div>
                <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">Attendance Reports</h1>
                <p class="text-sm text-slate-500">Trend analysis, zone breakdown, and date-filtered summaries.</p>
            </div>
        </div>
        <a href="{{ route('attendance.reports.export', request()->only('from', 'to')) }}"
           class="btn-secondary text-sm">
            <i class="fa-solid fa-file-csv mr-1.5"></i>Export CSV
        </a>
    </div>

    {{-- Date range filter --}}
    <article class="surface-card p-4 mb-6">
        <form method="GET" action="{{ route('attendance.reports') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-slate-500 mb-1">From</label>
                <input type="date" name="from" class="form-input" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">To</label>
                <input type="date" name="to" class="form-input" value="{{ $to->format('Y-m-d') }}">
            </div>
            <button class="btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass mr-1"></i>Apply</button>
            <a href="{{ route('attendance.reports') }}" class="btn-secondary text-slate-500">Reset</a>
        </form>
    </article>

    {{-- Stat cards --}}
    <div class="mb-8 grid gap-4 grid-cols-2 md:grid-cols-4">
        <article class="stat-card">
            <p class="text-xs text-slate-500">Present</p>
            <p class="mt-1 text-3xl font-semibold text-green-600">{{ $presentCount }}</p>
        </article>
        <article class="stat-card">
            <p class="text-xs text-slate-500">Late</p>
            <p class="mt-1 text-3xl font-semibold text-amber-500">{{ $lateCount }}</p>
        </article>
        <article class="stat-card">
            <p class="text-xs text-slate-500">Excused</p>
            <p class="mt-1 text-3xl font-semibold text-blue-500">{{ $excusedCount }}</p>
        </article>
        <article class="stat-card">
            <p class="text-xs text-slate-500">Absent</p>
            <p class="mt-1 text-3xl font-semibold text-red-500">{{ $absentCount }}</p>
        </article>
    </div>

    {{-- Trend chart --}}
    <article class="surface-card p-6 mb-6">
        <h3 class="mb-4 text-sm font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-chart-line mr-2 text-orange-500"></i>Monthly Attendance Trend (Last 12 Months)
        </h3>
        <canvas id="trendChart" height="80"></canvas>
    </article>

    {{-- Top members --}}
    @if ($topMembers->isNotEmpty())
    <article class="surface-card p-6 mb-6">
        <h3 class="mb-4 text-sm font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-medal mr-2 text-amber-500"></i>Top Attenders
        </h3>
        <div class="space-y-2">
            @foreach ($topMembers as $idx => $m)
                <div class="flex items-center gap-3">
                    <span class="w-6 text-center text-xs font-bold text-slate-400">{{ $idx + 1 }}</span>
                    <a href="{{ route('attendance.member.profile', $m->id) }}"
                       class="flex-1 text-sm text-[var(--color-ink-950)] hover:underline">{{ $m->full_name }}</a>
                    <span class="text-sm font-semibold text-green-600">{{ $m->times_present }}×</span>
                </div>
            @endforeach
        </div>
    </article>
    @endif

    {{-- Service breakdown table --}}
    <article class="surface-card p-6 overflow-x-auto">
        <h3 class="mb-4 text-sm font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-table mr-2 text-[var(--color-brand-600)]"></i>By Service
        </h3>
        <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
            <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Service</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3 text-center">Present</th>
                    <th class="px-4 py-3 text-center">Late</th>
                    <th class="px-4 py-3 text-center">Excused</th>
                    <th class="px-4 py-3 text-center">Absent</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                @forelse ($byService as $svc)
                    <tr class="hover:bg-[var(--color-surface-50)]">
                        <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $svc->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $svc->service_date?->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center text-green-600">{{ $svc->present_count }}</td>
                        <td class="px-4 py-3 text-center text-amber-500">{{ $svc->late_count }}</td>
                        <td class="px-4 py-3 text-center text-blue-500">{{ $svc->excused_count }}</td>
                        <td class="px-4 py-3 text-center text-red-500">{{ $svc->absent_count }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('attendance.services.show', $svc) }}"
                               class="text-[var(--color-brand-600)] hover:underline text-xs">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No services in this date range.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-5">{{ $byService->links() }}</div>
    </article>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        const trendData = @json($trend);
        const labels    = trendData.map(d => d.month);
        const totals    = trendData.map(d => d.total);

        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Present',
                    data: totals,
                    borderColor: 'rgb(22,163,74)',
                    backgroundColor: 'rgba(22,163,74,0.08)',
                    borderWidth: 2,
                    pointRadius: 4,
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>

