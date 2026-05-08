<x-layouts.app title="Reports">

    {{-- Page header --}}
    <div class="mb-6 flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(167,139,250,0.14);">
            <i class="fas fa-chart-bar text-lg" style="color:rgba(167,139,250,0.9);"></i>
        </span>
        <div>
            <h1 class="text-2xl font-bold text-[var(--color-ink-950)]">Reports</h1>
            <p class="text-xs text-slate-500">Exportable data reports across ministry modules</p>
        </div>
    </div>

    <section class="grid gap-5 md:grid-cols-2">

        <a href="{{ route('reports.departments') }}" class="surface-card p-6 block transition hover:-translate-y-0.5">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-sitemap text-base" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Department Report</h3>
            </div>
            <p class="text-sm text-slate-500">Membership, attendance, and completed assignment summary by department.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(244,193,93,0.8);">
                Open report <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </a>

        <a href="{{ route('reports.zones') }}" class="surface-card p-6 block transition hover:-translate-y-0.5">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-map-marked-alt text-base" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Zone Report</h3>
            </div>
            <p class="text-sm text-slate-500">Member and attendance activity by zone.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(52,211,153,0.8);">
                Open report <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </a>

        <a href="{{ route('reports.events') }}" class="surface-card p-6 block transition hover:-translate-y-0.5">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.14);">
                    <i class="fas fa-calendar-alt text-base" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Event Report</h3>
            </div>
            <p class="text-sm text-slate-500">Event status, registration count, and volunteer allocation.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(36,184,255,0.8);">
                Open report <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </a>

        <a href="{{ route('reports.volunteers') }}" class="surface-card p-6 block transition hover:-translate-y-0.5">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(255,111,145,0.14);">
                    <i class="fas fa-hands-helping text-base" style="color:rgba(255,111,145,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Volunteer Report</h3>
            </div>
            <p class="text-sm text-slate-500">Assignment lifecycle and operational breakdown for volunteers.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(255,111,145,0.8);">
                Open report <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </a>

    </section>
</x-layouts.app>
