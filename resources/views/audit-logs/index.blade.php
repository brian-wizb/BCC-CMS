<x-layouts.app title="Audit Log">
    <section class="space-y-5">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-history" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Audit Log</h3>
                    <p class="text-xs text-slate-500">Track every system action — who did what and when</p>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('audit-logs.index') }}" class="surface-card p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="form-label text-xs">Search user / entity</label>
                    <input name="search" class="form-input" placeholder="Username, entity…" value="{{ $search }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="form-label text-xs">Action</label>
                    <select name="action" class="form-input">
                        <option value="">All actions</option>
                        @foreach ($actions as $act)
                            <option value="{{ $act }}" @selected($action === $act)>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs">From</label>
                    <input type="date" name="date_from" class="form-input" value="{{ $dateFrom }}">
                </div>
                <div>
                    <label class="form-label text-xs">To</label>
                    <input type="date" name="date_to" class="form-input" value="{{ $dateTo }}">
                </div>
                <button type="submit" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                @if ($search || $action || $dateFrom || $dateTo)
                    <a href="{{ route('audit-logs.index') }}" class="btn-secondary flex items-center gap-1.5 text-rose-400">
                        <i class="fas fa-times text-xs"></i> Clear
                    </a>
                @endif
            </div>
        </form>

        <div class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">When</th>
                            <th class="px-4 py-3">Actor</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Entity</th>
                            <th class="px-4 py-3">IP</th>
                            <th class="px-4 py-3">Changes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($logs as $log)
                            <tr class="align-top hover:bg-[var(--color-surface-50)] transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-slate-500 text-xs">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-[var(--color-ink-950)] whitespace-nowrap">
                                    {{ $log->actor_username ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $actionColor = match (true) {
                                            str_contains($log->action, 'create') || str_contains($log->action, 'granted') || str_contains($log->action, 'restore') => 'text-emerald-400',
                                            str_contains($log->action, 'delete') || str_contains($log->action, 'revoked') => 'text-rose-400',
                                            str_contains($log->action, 'update') || str_contains($log->action, 'password') => 'text-amber-400',
                                            default => 'text-slate-400',
                                        };
                                    @endphp
                                    <code class="rounded px-1.5 py-0.5 text-xs font-mono bg-[var(--color-surface-200)] {{ $actionColor }}">
                                        {{ $log->action }}
                                    </code>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs whitespace-nowrap">
                                    {{ $log->entity_type }}
                                    @if ($log->entity_id)
                                        <span class="text-slate-500">#{{ $log->entity_id }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs whitespace-nowrap">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs max-w-[320px]">
                                    @if ($log->before_json || $log->after_json)
                                        <details class="cursor-pointer">
                                            <summary class="text-slate-400 hover:text-[var(--color-ink-950)] transition-colors">View diff</summary>
                                            <div class="mt-2 space-y-1">
                                                @if ($log->before_json)
                                                    <div>
                                                        <p class="text-[10px] font-semibold uppercase text-rose-400 mb-0.5">Before</p>
                                                        <pre class="whitespace-pre-wrap break-all text-slate-400 text-[10px] bg-[var(--color-surface-100)] rounded p-2">{{ json_encode($log->before_json, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                                @if ($log->after_json)
                                                    <div>
                                                        <p class="text-[10px] font-semibold uppercase text-emerald-400 mb-0.5">After</p>
                                                        <pre class="whitespace-pre-wrap break-all text-slate-400 text-[10px] bg-[var(--color-surface-100)] rounded p-2">{{ json_encode($log->after_json, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-400">No audit log entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[var(--color-surface-200)] px-4 py-4">
                {{ $logs->links() }}
            </div>
        </div>

    </section>
</x-layouts.app>
