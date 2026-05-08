<x-layouts.app title="Zones">
    <section class="surface-card p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Zones</p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">Church zones</h3>
                <p class="mt-2 text-sm text-slate-500">Total zones: {{ $zones->total() }}</p>
            </div>

            <a href="{{ route('zones.create') }}" class="btn-primary">Add zone</a>
        </div>

        <form method="GET" action="{{ route('zones.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_auto]">
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
                    @forelse ($zones as $zone)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-medium text-[var(--color-ink-950)]">{{ $zone->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $zone->description ?: 'No description' }}</p>
                            </td>
                            <td class="px-4 py-4 text-slate-500">{{ $zone->leader?->full_name ?: $zone->leader?->username ?: '—' }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ $zone->memberships_count }}</td>
                            <td class="px-4 py-4"><x-ui.status-badge :status="$zone->status" /></td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('zones.show', $zone) }}" class="btn-secondary">View</a>
                                    <a href="{{ route('zones.edit', $zone) }}" class="btn-secondary">Edit</a>
                                    <form method="POST" action="{{ route('zones.destroy', $zone) }}" onsubmit="return confirm('Delete this zone?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary text-red-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No zones found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $zones->links() }}</div>
    </section>
</x-layouts.app>
