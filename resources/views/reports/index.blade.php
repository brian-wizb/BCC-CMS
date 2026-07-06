<x-layouts.app title="Reports">

    <div class="mb-6 flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(167,139,250,0.14);">
            <i class="fas fa-chart-bar text-lg" style="color:rgba(167,139,250,0.9);"></i>
        </span>
        <div>
            <h1 class="text-2xl font-bold text-[var(--color-ink-950)]">Reports</h1>
            <p class="text-xs text-slate-500">Exportable data reports across all ministry modules — filterable by date range</p>
        </div>
    </div>

    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

        {{-- Members --}}
        <a href="{{ route('reports.members') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.14);">
                    <i class="fas fa-users text-base" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Members Report</h3>
            </div>
            <p class="text-sm text-slate-500">Growth trends, marital and employment filters, plus university student timelines.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(36,184,255,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Finance --}}
        <a href="{{ route('reports.finance') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-coins text-base" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Finance Report</h3>
            </div>
            <p class="text-sm text-slate-500">Income, expenditure, donations, department finances, and monthly trends.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(52,211,153,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Pledges --}}
        <a href="{{ route('reports.pledges') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-hand-holding-usd text-base" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Pledges & Giving</h3>
            </div>
            <p class="text-sm text-slate-500">Pledge fulfillment rate, collections, outstanding balances, and zone breakdown.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(244,193,93,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Visitors --}}
        <a href="{{ route('reports.visitors') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(167,139,250,0.14);">
                    <i class="fas fa-user-friends text-base" style="color:rgba(167,139,250,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Visitors Report</h3>
            </div>
            <p class="text-sm text-slate-500">Visitor flow, status breakdown, and conversion-to-member rate.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(167,139,250,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Follow-up --}}
        <a href="{{ route('reports.followup') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(255,111,145,0.14);">
                    <i class="fas fa-tasks text-base" style="color:rgba(255,111,145,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Follow-Up Report</h3>
            </div>
            <p class="text-sm text-slate-500">Task completion rates, overdue follow-ups, and team performance by assignee.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(255,111,145,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Attendance (existing, linked from hub) --}}
        <a href="{{ route('attendance.reports') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(29,214,255,0.14);">
                    <i class="fas fa-calendar-check text-base" style="color:rgba(29,214,255,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Attendance Report</h3>
            </div>
            <p class="text-sm text-slate-500">Service attendance records by member, zone, and department with CSV export.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(29,214,255,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Departments --}}
        <a href="{{ route('reports.departments') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-sitemap text-base" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Department Report</h3>
            </div>
            <p class="text-sm text-slate-500">Membership, attendance, and completed assignment summary by department.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(244,193,93,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Zones --}}
        <a href="{{ route('reports.zones') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-map-marked-alt text-base" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Zone Report</h3>
            </div>
            <p class="text-sm text-slate-500">Member count, attendance activity, and attendance rate by zone.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(52,211,153,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Groups --}}
        <a href="{{ route('reports.groups') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-users text-base" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Groups Report</h3>
            </div>
            <p class="text-sm text-slate-500">Registered vs guest composition and leadership distribution across groups.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(99,102,241,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Events --}}
        <a href="{{ route('reports.events') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.14);">
                    <i class="fas fa-calendar-alt text-base" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Event Report</h3>
            </div>
            <p class="text-sm text-slate-500">Event status, registration count, and volunteer allocation.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(36,184,255,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Volunteers --}}
        <a href="{{ route('reports.volunteers') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(255,111,145,0.14);">
                    <i class="fas fa-hands-helping text-base" style="color:rgba(255,111,145,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Volunteer Report</h3>
            </div>
            <p class="text-sm text-slate-500">Assignment lifecycle and operational breakdown for volunteers.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(255,111,145,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Pastoral Care --}}
        <a href="{{ route('reports.pastoral') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(251,146,60,0.14);">
                    <i class="fas fa-pray text-base" style="color:rgba(251,146,60,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Pastoral Care Report</h3>
            </div>
            <p class="text-sm text-slate-500">Case volume, priority breakdown, and assignee resolution performance.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(251,146,60,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Communications --}}
        <a href="{{ route('reports.communications') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-paper-plane text-base" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Communications Report</h3>
            </div>
            <p class="text-sm text-slate-500">Message volume, delivery rates, and channel breakdown by period.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(99,102,241,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

        {{-- Payroll --}}
        <a href="{{ route('reports.payroll') }}" class="surface-card p-6 block">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(20,184,166,0.14);">
                    <i class="fas fa-money-bill-wave text-base" style="color:rgba(20,184,166,0.9);"></i>
                </span>
                <h3 class="text-lg font-semibold text-[var(--color-ink-950)]">Payroll Report</h3>
            </div>
            <p class="text-sm text-slate-500">Staff salaries, net pay, PAYE deductions, and payment status.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(20,184,166,0.8);">Open report <i class="fas fa-arrow-right text-[10px]"></i></p>
        </a>

    </section>
</x-layouts.app>
