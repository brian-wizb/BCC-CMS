<x-layouts.app title="Employees">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.12);">
                    <i class="fas fa-user-tie text-base" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance &rsaquo; Payroll</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Employees</h3>
                </div>
            </div>
            <a href="{{ route('employees.create') }}" class="btn-primary flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i> Add Employee
            </a>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative min-w-[180px] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input name="search" class="form-input w-full pl-8" value="{{ $search ?? '' }}" placeholder="Name, designation or phone...">
            </div>
            <button type="submit" class="btn-secondary">Search</button>
            @if(!empty($search))
                <a href="{{ route('employees.index') }}" class="btn-secondary flex items-center gap-1"><i class="fas fa-times text-xs"></i></a>
            @endif
            <div class="ml-auto flex items-center gap-2 text-sm text-slate-500">
                <span class="whitespace-nowrap">Show</span>
                <select name="per_page" onchange="this.form.submit()" class="form-input py-1.5 text-sm w-auto">
                    @foreach([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" @selected(($perPage ?? 20) == $n)>{{ $n }}</option>
                    @endforeach
                </select>
                <span>entries</span>
            </div>
        </form>

        <article class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3"><i class="fas fa-user mr-1.5 opacity-60"></i>Name</th>
                            <th class="px-5 py-3"><i class="fas fa-briefcase mr-1.5 opacity-60"></i>Designation</th>
                            <th class="px-5 py-3"><i class="fas fa-phone mr-1.5 opacity-60"></i>Phone</th>
                            <th class="px-5 py-3">Account Name</th>
                            <th class="px-5 py-3">Account No.</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $employees->firstItem() + $loop->index }}</td>
                            <td class="px-5 py-3.5 font-medium text-[var(--color-ink-950)]">{{ $employee->name }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $employee->designation ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $employee->phone ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $employee->account_name ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $employee->account_number ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('employees.edit', $employee) }}" class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-user-tie mb-2 block text-2xl text-slate-300"></i>
                                No employees yet. <a href="{{ route('employees.create') }}" class="text-blue-600 underline">Add the first one</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($employees->hasPages())
            <div class="border-t border-[var(--color-surface-200)] px-5 py-4">
                {{ $employees->links() }}
            </div>
            @endif
        </article>
    </div>
</x-layouts.app>
