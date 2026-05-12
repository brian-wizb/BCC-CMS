<x-layouts.app title="Follow-Up Report">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(255,111,145,0.14);">
                    <i class="fas fa-tasks" style="color:rgba(255,111,145,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Follow-Up Report</h3>
                    <p class="text-xs text-slate-500">Task completion rates, overdue follow-ups, and team performance</p>
                </div>
            </div>
            <div class="flex items-center gap-2 print-hide">
                <a href="{{ route('reports.followup.export', request()->query()) }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <button onclick="window.print()" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-print text-xs"></i> Print
                </button>
                <a href="{{ route('reports.index') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs"></i> All reports
                </a>
            </div>
        </div>

        <x-ui.date-range-filters :action="route('reports.followup')" :date-from="$dateFrom" :date-to="$dateTo" />

        {{-- Stat cards --}}
        <article class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total Tasks</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($total) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Open</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($open) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">In Progress</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-brand-500)]">{{ number_format($inProgress) }}</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Completed</p>
                <p class="mt-1 text-2xl font-bold text-emerald-400">{{ number_format($completed) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $completionRate }}% rate</p>
            </div>
            <div class="stat-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Overdue</p>
                <p class="mt-1 text-2xl font-bold {{ $overdue > 0 ? 'text-rose-400' : 'text-slate-400' }}">{{ number_format($overdue) }}</p>
                <p class="mt-1 text-xs text-slate-500">Not completed past due date</p>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- By assignee --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-user-check opacity-60"></i> Performance by Assignee
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="pb-3">Assignee</th>
                                <th class="pb-3 text-right">Total</th>
                                <th class="pb-3 text-right">Done</th>
                                <th class="pb-3 text-right">Overdue</th>
                                <th class="pb-3 text-right">Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @forelse ($byAssignee as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ $row->name }}</td>
                                    <td class="py-2.5 text-right">{{ $row->total }}</td>
                                    <td class="py-2.5 text-right text-emerald-400">{{ $row->completed_count }}</td>
                                    <td class="py-2.5 text-right {{ $row->overdue_count > 0 ? 'text-rose-400' : 'text-slate-500' }}">{{ $row->overdue_count }}</td>
                                    <td class="py-2.5 text-right font-semibold">{{ $row->total > 0 ? round($row->completed_count / $row->total * 100) : 0 }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 text-center text-slate-400">No assignees found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            {{-- By task type --}}
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-layer-group opacity-60"></i> By Task Type
                </h4>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pb-3">Type</th>
                            <th class="pb-3 text-right">Count</th>
                            <th class="pb-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($byType as $row)
                            <tr>
                                <td class="py-2.5 font-medium text-[var(--color-ink-950)]">{{ ucwords(str_replace('_', ' ', $row->task_type)) }}</td>
                                <td class="py-2.5 text-right">{{ $row->total }}</td>
                                <td class="py-2.5 text-right text-slate-500">{{ $total > 0 ? round($row->total / $total * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">—</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </article>
        </div>

        {{-- Overdue tasks --}}
        @if ($recentOverdue->isNotEmpty())
            <article class="surface-card p-6">
                <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-[var(--color-ink-950)]">
                    <i class="fas fa-exclamation-triangle text-rose-400"></i> Overdue Tasks
                    <span class="ml-1 rounded-full bg-rose-500/20 px-2 py-0.5 text-xs text-rose-400">{{ $overdue }}</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-3 pb-3">Type</th>
                                <th class="px-3 pb-3">Priority</th>
                                <th class="px-3 pb-3">Assigned To</th>
                                <th class="px-3 pb-3">Due Date</th>
                                <th class="px-3 pb-3">Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @foreach ($recentOverdue as $task)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-[var(--color-ink-950)]">{{ ucwords(str_replace('_', ' ', $task->task_type)) }}</td>
                                    <td class="px-3 py-3"><x-ui.status-badge :status="$task->priority" /></td>
                                    <td class="px-3 py-3">{{ $task->assignee?->full_name ?: '—' }}</td>
                                    <td class="px-3 py-3 text-rose-400">{{ optional($task->due_date)->format('d M Y') ?: '—' }}</td>
                                    <td class="px-3 py-3 font-semibold text-rose-400">{{ optional($task->due_date)->diffInDays(now()) }}d</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @endif

    </section>
</x-layouts.app>
