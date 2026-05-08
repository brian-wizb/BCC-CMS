<x-layouts.app title="Alerts">
    <section class="surface-card p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Leadership Tools</p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Alerts</h3>
                <p class="mt-2 text-sm text-slate-500">Open and monitor member care alerts.</p>
            </div>
            <form method="POST" action="{{ route('alerts.run') }}">
                @csrf
                <button type="submit" class="btn-primary">Run alert generator</button>
            </form>
        </div>

        <form method="GET" action="{{ route('alerts.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[220px_auto]">
            <select name="status" class="form-input">
                <option value="">All statuses</option>
                @foreach (['open', 'acknowledged', 'resolved'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Alert</th>
                        <th class="px-4 py-3">Severity</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Assignee / Due</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($alerts as $alert)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-medium text-[var(--color-ink-950)]">{{ $alert->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $alert->message }}</p>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $severityClasses = [
                                        'low' => 'bg-slate-100 text-slate-700',
                                        'medium' => 'bg-amber-100 text-amber-700',
                                        'high' => 'bg-orange-100 text-orange-700',
                                        'critical' => 'bg-rose-100 text-rose-700',
                                    ];
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $severityClasses[$alert->severity] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($alert->severity) }}
                                </span>
                            </td>
                            <td class="px-4 py-4"><x-ui.status-badge :status="$alert->status" /></td>
                            <td class="px-4 py-4 text-slate-500">
                                {{ $alert->assignee?->full_name ?: ($alert->assignee?->username ?: 'Unassigned') }}<br>
                                {{ optional($alert->due_at)->format('d M Y H:i') ?: 'No due date' }}
                            </td>
                            <td class="px-4 py-4">
                                <form method="POST" action="{{ route('alerts.update', $alert) }}" class="grid gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-input" required>
                                        @foreach (['open', 'acknowledged', 'resolved'] as $statusOption)
                                            <option value="{{ $statusOption }}" @selected($alert->status === $statusOption)>{{ ucfirst($statusOption) }}</option>
                                        @endforeach
                                    </select>
                                    <select name="severity" class="form-input" required>
                                        @foreach (['low', 'medium', 'high', 'critical'] as $severityOption)
                                            <option value="{{ $severityOption }}" @selected($alert->severity === $severityOption)>{{ ucfirst($severityOption) }}</option>
                                        @endforeach
                                    </select>
                                    <select name="assigned_to" class="form-input">
                                        <option value="">Unassigned</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" @selected((string) $alert->assigned_to === (string) $user->id)>
                                                {{ $user->full_name ?: $user->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="datetime-local" name="due_at" class="form-input" value="{{ $alert->due_at ? $alert->due_at->format('Y-m-d\\TH:i') : '' }}">
                                    <button type="submit" class="btn-secondary">Update</button>
                                </form>
                                <form method="POST" action="{{ route('alerts.destroy', $alert) }}" class="mt-2" onsubmit="return confirm('Delete this alert?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No alerts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $alerts->links() }}</div>
    </section>
</x-layouts.app>
