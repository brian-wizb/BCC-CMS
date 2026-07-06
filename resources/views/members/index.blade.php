<x-layouts.app title="Members">
    <section class="surface-card p-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.12);">
                    <i class="fas fa-users text-lg" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Members</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Church members
                        <span class="ml-1.5 rounded-full px-2 py-0.5 text-xs font-medium" style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">{{ $members->total() }}</span>
                    </h3>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('members.create') }}" class="btn-primary flex items-center gap-1.5">
                    <i class="fas fa-user-plus text-xs"></i> Add member
                </a>
                <a href="{{ route('reports.members') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-chart-bar text-xs"></i> Members report
                </a>
                <a href="{{ route('members.export') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- Search + Reporting Filters + Import --}}
        <div class="mt-5 grid gap-4">
            <form method="GET" action="{{ route('members.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div class="relative xl:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input name="search" class="form-input pl-8" placeholder="Search by name, phone, or zone" value="{{ $search }}">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">From</label>
                    <input type="date" name="date_from" class="form-input" value="{{ $dateFrom }}">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">To</label>
                    <input type="date" name="date_to" class="form-input" value="{{ $dateTo }}">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Marital status</label>
                    <select name="marital_status" class="form-input">
                        <option value="">All members</option>
                        @foreach (['Single', 'Married', 'Divorced', 'Widowed', 'Separated', 'Unknown'] as $status)
                            <option value="{{ $status }}" @selected($maritalStatus === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Employment status</label>
                    <select name="employment_status" class="form-input">
                        <option value="">All employment statuses</option>
                        @foreach (['Employed', 'Unemployed', 'Entrepreneur', 'Self-employed', 'Student', 'Retired', 'Other'] as $status)
                            <option value="{{ $status }}" @selected($employmentStatus === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">University student</label>
                    <select name="university_student" class="form-input">
                        <option value="">All</option>
                        <option value="yes" @selected($universityStudent === 'yes')>Yes</option>
                        <option value="no" @selected($universityStudent === 'no')>No</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-secondary flex items-center gap-1.5">
                        <i class="fas fa-filter text-xs"></i> Apply
                    </button>
                    <a href="{{ route('members.index') }}" class="btn-secondary flex items-center gap-1.5">Clear</a>
                </div>
            </form>

            <form method="POST" action="{{ route('members.import') }}" enctype="multipart/form-data" class="flex gap-2">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" class="form-input max-w-xs">
                <button type="submit" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-upload text-xs"></i> Import CSV
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-user mr-1.5 opacity-60"></i>Name</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-venus-mars mr-1.5 opacity-60"></i>Gender</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-phone mr-1.5 opacity-60"></i>Phone</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-map-marker-alt mr-1.5 opacity-60"></i>Zone</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-home mr-1.5 opacity-60"></i>Residency</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-ring mr-1.5 opacity-60"></i>Marital status</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-briefcase mr-1.5 opacity-60"></i>Employment</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-university mr-1.5 opacity-60"></i>University details</th>
                        <th class="px-4 py-3 font-medium"><i class="fas fa-ellipsis-h mr-1.5 opacity-60"></i>Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($members as $member)
                        <tr class="hover:bg-[var(--color-surface-50)] transition">
                            <td class="px-4 py-3.5">
                                <span class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold" style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">
                                        {{ mb_strtoupper(mb_substr($member->full_name, 0, 1)) }}
                                    </span>
                                    <span class="font-medium text-[var(--color-ink-950)]">{{ $member->full_name }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-500">
                                @if ($member->gender === 'Male')
                                    <i class="fas fa-mars mr-1 text-blue-400"></i>
                                @elseif ($member->gender === 'Female')
                                    <i class="fas fa-venus mr-1 text-pink-400"></i>
                                @endif
                                {{ $member->gender }}
                            </td>
                            <td class="px-4 py-3.5 text-slate-500">{{ $member->phone ?: '—' }}</td>
                            <td class="px-4 py-3.5 text-slate-500">{{ $member->zone ?: '—' }}</td>
                            <td class="px-4 py-3.5 text-slate-500">{{ $member->residency ?: '—' }}</td>
                            <td class="px-4 py-3.5 text-slate-500">{{ $member->marital_status ?: '—' }}</td>
                            <td class="px-4 py-3.5 text-slate-500">{{ $member->employment_status ?: '—' }}</td>
                            <td class="px-4 py-3.5 text-slate-500">
                                @if ($member->is_university_student)
                                    <div class="space-y-0.5">
                                        <p class="font-medium text-[var(--color-ink-950)]">{{ $member->university?->name ?: 'University not set' }}</p>
                                        <p class="text-xs text-slate-400">
                                            {{ optional($member->university_start_date)->format('d M Y') ?: '—' }}
                                            →
                                            {{ optional($member->university_end_date)->format('d M Y') ?: '—' }}
                                        </p>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1.5">
                                    <a href="{{ route('members.show', $member) }}" class="btn-secondary flex items-center gap-1 py-1 px-2.5 text-xs">
                                        <i class="fas fa-eye text-[10px]"></i> View
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="btn-secondary flex items-center gap-1 py-1 px-2.5 text-xs">
                                        <i class="fas fa-pen text-[10px]"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}" data-confirm="Delete {{ $member->full_name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary flex items-center gap-1 py-1 px-2.5 text-xs text-red-500 hover:bg-red-50">
                                            <i class="fas fa-trash text-[10px]"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center">
                                <i class="fas fa-users-slash mb-2 block text-2xl text-slate-300"></i>
                                <p class="text-slate-400">No members found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $members->links() }}
        </div>
    </section>
</x-layouts.app>
