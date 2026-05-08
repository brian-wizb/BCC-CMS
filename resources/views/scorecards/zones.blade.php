<x-layouts.app title="Zone Scorecards">
    <section class="surface-card p-6 overflow-x-auto">

        {{-- Card header --}}
        <div class="mb-5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-map-marked-alt" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <div>
                    <h2 class="text-xl font-semibold text-[var(--color-ink-950)]">Zone Scorecards</h2>
                    <p class="text-xs text-slate-500">Activity &amp; attendance by zone</p>
                </div>
            </div>
            <a href="{{ route('scorecards.index') }}" class="btn-secondary flex items-center gap-1.5 text-xs">
                <i class="fas fa-arrow-left text-[10px]"></i> Back
            </a>
        </div>

        <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
            <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3"><i class="fas fa-map-marker-alt mr-1.5 opacity-60"></i>Zone</th>
                    <th class="px-4 py-3"><i class="fas fa-users mr-1.5 opacity-60"></i>Members</th>
                    <th class="px-4 py-3"><i class="fas fa-calendar-check mr-1.5 opacity-60"></i>Attendance Records</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                @forelse ($zones as $zone)
                    <tr>
                        <td class="px-4 py-4">{{ $zone['name'] }}</td>
                        <td class="px-4 py-4">{{ $zone['member_count'] }}</td>
                        <td class="px-4 py-4">{{ $zone['attendance_count'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No zone scorecard data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
