<x-layouts.app title="Department Report">
    <section class="surface-card p-6">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-sitemap" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Department Report</h3>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.departments.export') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </a>
                <a href="{{ route('reports.index') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs"></i> All reports
                </a>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3"><i class="fas fa-sitemap mr-1.5 opacity-60"></i>Department</th>
                        <th class="px-4 py-3"><i class="fas fa-users mr-1.5 opacity-60"></i>Members</th>
                        <th class="px-4 py-3"><i class="fas fa-calendar-check mr-1.5 opacity-60"></i>Attendance Records</th>
                        <th class="px-4 py-3"><i class="fas fa-tasks mr-1.5 opacity-60"></i>Completed Assignments</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($departments as $department)
                        <tr>
                            <td class="px-4 py-4 font-medium text-[var(--color-ink-950)]">{{ $department['name'] }}</td>
                            <td class="px-4 py-4">{{ $department['members'] }}</td>
                            <td class="px-4 py-4">{{ $department['attendance'] }}</td>
                            <td class="px-4 py-4">{{ $department['completed_assignments'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No department records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
