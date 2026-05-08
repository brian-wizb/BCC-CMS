<x-layouts.app title="Visitors">
    <section class="surface-card p-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                    <i class="fa-solid fa-users mr-1"></i> Visitors + Follow-up
                </p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-person-walking-arrow-right mr-2" style="color:#2563eb;"></i>
                    Visitors Register
                </h3>
                <p class="mt-1 text-sm text-slate-500">Total: {{ $visitors->total() }} visitors</p>
            </div>
            <a href="{{ route('visitors.create') }}" class="btn-primary">
                <i class="fa-solid fa-user-plus mr-1"></i> Add visitor
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('visitors.index') }}"
              class="mt-6 grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto]">
            <input name="search" class="form-input" value="{{ $search }}"
                   placeholder="Search name, phone, email, invited by…">
            <select name="status" class="form-input">
                <option value="">All statuses</option>
                @foreach (['new' => 'New', 'contacted' => 'Contacted', 'counseled' => 'Counseled', 'joined_zone' => 'Joined Zone', 'in_class' => 'In Class', 'converted' => 'Converted'] as $val => $label)
                    <option value="{{ $val }}" @selected($status === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
        </form>

        {{-- Table --}}
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Gender</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Invited by</th>
                        <th class="px-4 py-3">First visit</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($visitors as $visitor)
                        <tr class="hover:bg-[var(--color-surface-50)]">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-[var(--color-ink-950)]">{{ $visitor->full_name }}</p>
                            </td>
                            <td class="px-4 py-4 text-slate-500">
                                @if ($visitor->gender === 'male')
                                    <i class="fa-solid fa-mars text-blue-500"></i> Male
                                @elseif ($visitor->gender === 'female')
                                    <i class="fa-solid fa-venus text-pink-500"></i> Female
                                @else
                                    {{ ucfirst($visitor->gender ?: '—') }}
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-500">
                                @if ($visitor->phone)
                                    <p><i class="fa-solid fa-phone mr-1 text-xs"></i>{{ $visitor->phone }}</p>
                                @endif
                                @if ($visitor->email)
                                    <p><i class="fa-solid fa-envelope mr-1 text-xs"></i>{{ $visitor->email }}</p>
                                @endif
                                @if (! $visitor->phone && ! $visitor->email) — @endif
                            </td>
                            <td class="px-4 py-4 text-slate-500">{{ $visitor->invited_by ?: '—' }}</td>
                            <td class="px-4 py-4 text-slate-500">
                                {{ optional($visitor->first_visit_date)->format('d M Y') ?: '—' }}
                            </td>
                            <td class="px-4 py-4"><x-ui.status-badge :status="$visitor->status" /></td>
                            <td class="px-4 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('visitors.show', $visitor) }}"
                                       class="rounded-lg border border-[var(--color-surface-200)] px-2 py-1 text-xs hover:bg-[var(--color-surface-50)]"
                                       title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('visitors.edit', $visitor) }}"
                                       class="rounded-lg border border-[var(--color-surface-200)] px-2 py-1 text-xs hover:bg-[var(--color-surface-50)]"
                                       title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash mb-2 text-2xl"></i>
                                <p>No visitors found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $visitors->links() }}</div>
    </section>
</x-layouts.app>
