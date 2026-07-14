<x-layouts.app title="Counsellor Dashboard">
    <div class="dashboard-responsive">
    <section class="dashboard-hero">
        <div class="dashboard-hero-orb dashboard-hero-orb-a"></div>
        <div class="dashboard-hero-orb dashboard-hero-orb-b"></div>
        <div class="dashboard-hero-orb dashboard-hero-orb-c"></div>
        <div class="dashboard-hero-orb dashboard-hero-orb-d"></div>
        <div class="absolute inset-0 opacity-20 [background-image:repeating-linear-gradient(90deg,rgba(255,255,255,0.18)_0_1px,transparent_1px_24px)]"></div>
        <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="status-pill dashboard-hero-kicker">Counsellor Activity</span>
                <h1 class="dashboard-hero-title mt-3 text-3xl font-bold md:text-4xl">Welcome back, {{ $user->full_name }}</h1>
                <p class="dashboard-hero-copy mt-2 max-w-3xl text-sm md:text-base">
                    Your follow-up assignments and task progress are shown here. Focus on the activities assigned to you and update them as you complete them.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 dashboard-hero-stats-grid">
                <div class="dashboard-hero-stat">
                    <div class="dashboard-hero-stat-label">Assigned Tasks</div>
                    <div class="text-xl font-bold">{{ number_format($assignedTaskCount) }}</div>
                </div>
                <div class="dashboard-hero-stat">
                    <div class="dashboard-hero-stat-label">Overdue Tasks</div>
                    <div class="text-xl font-bold">{{ number_format($overdueTaskCount) }}</div>
                </div>
            </div>
        </div>
    </section>

    @if (! $leader)
        <section class="mt-6 surface-card p-6 border border-amber-200 bg-amber-50 text-amber-900">
            <h2 class="text-lg font-semibold">Leader profile not linked</h2>
            <p class="mt-2 text-sm">
                Your user account is not currently linked to a leader record, so your assigned follow-up tasks cannot be resolved to a counsellor profile.
                Ask an administrator to link this account to the correct leader record in the Leaders section.
            </p>
        </section>
    @endif

    <section class="mt-6 grid gap-4 xl:grid-cols-4 md:grid-cols-2">
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Pending</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($pendingTaskCount) }}</p><p class="mt-1 text-xs text-slate-500">Tasks waiting to start</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">In Progress</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($inProgressTaskCount) }}</p><p class="mt-1 text-xs text-slate-500">Tasks currently active</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Completed</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($completedTaskCount) }}</p><p class="mt-1 text-xs text-slate-500">Tasks finished</p></article>
        <article class="stat-card"><p class="text-xs uppercase tracking-[0.12em] text-slate-500">Overdue</p><p class="mt-2 text-3xl font-semibold text-[var(--color-ink-950)]">{{ number_format($overdueTaskCount) }}</p><p class="mt-1 text-xs text-slate-500">Tasks past due date</p></article>
    </section>

    <section class="mt-6 grid gap-4 xl:grid-cols-3">
        <article class="surface-card p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">My current tasks</h2>
                <a href="{{ route('follow-up.tasks') }}" class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            <div class="space-y-4">
                @forelse ($recentTasks as $task)
                    <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-[var(--color-ink-950)]">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $task->person_type }} #{{ $task->person_id }}</p>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold uppercase
                                @if ($task->status === 'pending') bg-amber-100 text-amber-700
                                @elseif ($task->status === 'in_progress') bg-blue-100 text-blue-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ str_replace('_', ' ', ucfirst($task->status)) }}
                            </span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                            @if ($task->due_date)
                            <span><i class="fa-solid fa-calendar-day mr-1"></i> Due {{ $task->due_date->format('d M Y') }}</span>
                            @endif
                            <span><i class="fa-solid fa-flag-checkered mr-1"></i> {{ ucfirst($task->priority) }} priority</span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
                        No assigned follow-up tasks found.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="surface-card p-6 xl:col-span-2">
            <h2 class="text-lg font-semibold">What to do next</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('follow-up.tasks') }}" class="surface-card rounded-2xl border border-slate-200 p-5 text-left hover:border-blue-200 hover:bg-blue-50 transition-colors">
                    <p class="text-sm font-semibold">Update task progress</p>
                    <p class="mt-2 text-xs text-slate-500">Open your assigned tasks list and mark them completed as you finish follow-up actions.</p>
                </a>
                <a href="{{ route('follow-up.tasks') }}" class="surface-card rounded-2xl border border-slate-200 p-5 text-left hover:border-purple-200 hover:bg-purple-50 transition-colors">
                    <p class="text-sm font-semibold">Record a follow-up action</p>
                    <p class="mt-2 text-xs text-slate-500">Use the task history form on a task card to log calls, prayers, or counselling activities.</p>
                </a>
            </div>
        </article>
    </section>
    </div>
</x-layouts.app>
