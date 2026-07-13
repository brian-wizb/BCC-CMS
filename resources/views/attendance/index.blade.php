<x-layouts.app title="Attendance">
    <div class="attendance-responsive">
    {{-- Icon header --}}
    <div class="mb-6 flex items-center gap-4 attendance-header">
        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--color-brand-600)] text-white shadow">
            <i class="fa-solid fa-calendar-check text-2xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">Attendance</h1>
            <p class="text-sm text-slate-500">Track services, record attendance, and view reports.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    {{-- Live stats --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="stat-card">
            <p class="text-sm text-slate-500">Total Services</p>
            <p class="mt-1 text-3xl font-semibold text-[var(--color-ink-950)]">{{ $totalServices }}</p>
        </article>
        <article class="stat-card">
            <p class="text-sm text-slate-500">Total Records</p>
            <p class="mt-1 text-3xl font-semibold text-[var(--color-ink-950)]">{{ $totalRecords }}</p>
        </article>
        <article class="stat-card">
            <p class="text-sm text-slate-500">Present This Month</p>
            <p class="mt-1 text-3xl font-semibold text-green-600">{{ $thisMonthPresent }}</p>
        </article>
        <article class="stat-card">
            <p class="text-sm text-slate-500">Last Service</p>
            <p class="mt-1 text-base font-semibold text-[var(--color-ink-950)] truncate">
                {{ $lastService?->name ?? 'None yet' }}
            </p>
            @if ($lastService)
                <p class="text-xs text-slate-400">{{ $lastService->service_date?->format('d M Y') }}</p>
            @endif
        </article>
    </div>

    {{-- Navigation cards --}}
    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('attendance.services') }}" class="surface-card p-6 flex items-start gap-4 hover:border-[var(--color-brand-400)] transition-colors">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                <i class="fa-solid fa-church text-lg"></i>
            </span>
            <div>
                <h3 class="font-semibold text-[var(--color-ink-950)]">Services</h3>
                <p class="mt-1 text-sm text-slate-500">Create and manage service sessions used for attendance tracking.</p>
            </div>
        </a>

        <a href="{{ route('attendance.bulk') }}" class="surface-card p-6 flex items-start gap-4 hover:border-[var(--color-brand-400)] transition-colors">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                <i class="fa-solid fa-list-check text-lg"></i>
            </span>
            <div>
                <h3 class="font-semibold text-[var(--color-ink-950)]">Bulk Attendance Sheet</h3>
                <p class="mt-1 text-sm text-slate-500">Record attendance for all members at once with a quick grid.</p>
            </div>
        </a>

        <a href="{{ route('attendance.record') }}" class="surface-card p-6 flex items-start gap-4 hover:border-[var(--color-brand-400)] transition-colors">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                <i class="fa-solid fa-user-check text-lg"></i>
            </span>
            <div>
                <h3 class="font-semibold text-[var(--color-ink-950)]">Record Attendance</h3>
                <p class="mt-1 text-sm text-slate-500">Capture individual attendance for members, visitors, or families.</p>
            </div>
        </a>

        <a href="{{ route('attendance.reports') }}" class="surface-card p-6 flex items-start gap-4 hover:border-[var(--color-brand-400)] transition-colors">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                <i class="fa-solid fa-chart-bar text-lg"></i>
            </span>
            <div>
                <h3 class="font-semibold text-[var(--color-ink-950)]">Reports</h3>
                <p class="mt-1 text-sm text-slate-500">Date-range reports with trend charts, zone breakdown and CSV export.</p>
            </div>
        </a>

        <a href="{{ route('attendance.scan') }}" class="surface-card p-6 flex items-start gap-4 hover:border-[var(--color-brand-400)] transition-colors">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                <i class="fa-solid fa-qrcode text-lg"></i>
            </span>
            <div>
                <h3 class="font-semibold text-[var(--color-ink-950)]">QR Scanner</h3>
                <p class="mt-1 text-sm text-slate-500">Usher scans member QR codes to auto-record attendance at the door.</p>
            </div>
        </a>

        @if ($lastService)
        <a href="{{ route('attendance.services.show', $lastService) }}" class="surface-card p-6 flex items-start gap-4 hover:border-[var(--color-brand-400)] transition-colors">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-100 text-pink-600">
                <i class="fa-solid fa-magnifying-glass-chart text-lg"></i>
            </span>
            <div>
                <h3 class="font-semibold text-[var(--color-ink-950)]">Last Service Detail</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $lastService->name }} — {{ $lastService->service_date?->format('d M Y') }}</p>
            </div>
        </a>
        @endif
    </section>
    </div>
</x-layouts.app>

