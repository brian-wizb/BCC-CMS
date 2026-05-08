<x-layouts.app title="Executive Dashboard">
    <section class="space-y-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Leadership Operations</p>
            <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Executive Dashboard</h3>
            <p class="mt-2 text-sm text-slate-500">High-level operational KPIs across membership, pastoral care, and ministry operations.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="stat-card"><p class="text-sm text-slate-500">Total Members</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ $kpis['members'] }}</p></article>
            <article class="stat-card"><p class="text-sm text-slate-500">Attendance Records</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ $kpis['attendance'] }}</p></article>
            <article class="stat-card"><p class="text-sm text-slate-500">Open Pastoral Cases</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ $kpis['pastoral_open_cases'] }}</p></article>
            <article class="stat-card"><p class="text-sm text-slate-500">Volunteer Assignments</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ $kpis['volunteer_assignments'] }}</p></article>
        </div>
    </section>
</x-layouts.app>
