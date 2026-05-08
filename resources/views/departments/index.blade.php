<x-layouts.app title="Departments">
    <section class="surface-card p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Departments</p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Ministry departments</h3>
                <p class="mt-2 text-sm text-slate-500">Total departments: {{ $departments->total() }}</p>
            </div>

            <a href="{{ route('departments.create') }}" class="btn-primary">Add department</a>
        </div>

        <form method="GET" action="{{ route('departments.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_auto]">
            <input name="search" class="form-input" placeholder="Search by name or description" value="{{ $search }}">
            <select name="status" class="form-input">
                <option value="">All statuses</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Leader</th>
                        <th class="px-4 py-3 font-medium">Members</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($departments as $department)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-medium text-[var(--color-ink-950)]">{{ $department->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $department->description ?: 'No description' }}</p>
                            </td>
                            <td class="px-4 py-4 text-slate-500">{{ $department->leader?->full_name ?: $department->leader?->username ?: '—' }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ $department->memberships_count }}</td>
                            <td class="px-4 py-4"><x-ui.status-badge :status="$department->status" /></td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('departments.show', $department) }}" class="btn-secondary">View</a>
                                    <a href="{{ route('departments.edit', $department) }}" class="btn-secondary">Edit</a>
                                    <form method="POST" action="{{ route('departments.destroy', $department) }}" onsubmit="return confirm('Delete this department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary text-red-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No departments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $departments->links() }}</div>
    </section>
</x-layouts.app>
