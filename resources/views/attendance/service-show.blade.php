<x-layouts.app :title="'Service: '.$service->name">
    <div class="attendance-responsive">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4 attendance-header">
            <a href="{{ route('attendance.services') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-600 text-white shadow">
                <i class="fa-solid fa-church text-xl"></i>
            </span>
            <div>
                <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">{{ $service->name }}</h1>
                <p class="text-sm text-slate-500">
                    {{ $service->service_date?->format('d M Y') }}
                    @if ($service->start_time)· {{ $service->start_time }}{{ $service->end_time ? ' – '.$service->end_time : '' }}@endif
                    @if ($service->location)· {{ $service->location }}@endif
                </p>
            </div>
        </div>
        <div class="flex gap-2 attendance-actions">
            <a href="{{ route('attendance.bulk', ['service_id' => $service->id]) }}" class="btn-secondary text-sm">
                <i class="fa-solid fa-list-check mr-1.5"></i>Bulk Sheet
            </a>
            <a href="{{ route('attendance.services.edit', $service) }}" class="btn-secondary text-sm">
                <i class="fa-solid fa-pen mr-1.5"></i>Edit
            </a>
        </div>
    </div>

    {{-- Stat row --}}
    <div class="mb-8 grid gap-4 grid-cols-2 md:grid-cols-4">
        <article class="stat-card text-center">
            <p class="text-xs text-slate-500">Present</p>
            <p class="mt-1 text-3xl font-semibold text-green-600">{{ $service->present_count }}</p>
        </article>
        <article class="stat-card text-center">
            <p class="text-xs text-slate-500">Late</p>
            <p class="mt-1 text-3xl font-semibold text-amber-500">{{ $service->late_count }}</p>
        </article>
        <article class="stat-card text-center">
            <p class="text-xs text-slate-500">Excused</p>
            <p class="mt-1 text-3xl font-semibold text-blue-500">{{ $service->excused_count }}</p>
        </article>
        <article class="stat-card text-center">
            <p class="text-xs text-slate-500">Absent</p>
            <p class="mt-1 text-3xl font-semibold text-red-500">{{ $service->absent_count }}</p>
        </article>
    </div>

    {{-- QR Check-in URL --}}
    <article class="surface-card p-4 mb-6 flex flex-wrap items-center gap-4">
        <i class="fa-solid fa-qrcode text-[var(--color-brand-600)] text-2xl"></i>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-[var(--color-ink-950)]">Self Check-In Link (QR)</p>
            <p class="text-xs text-slate-500 truncate">{{ $qrUrl }}</p>
        </div>
        <a href="{{ $qrUrl }}" target="_blank" class="btn-secondary text-xs">
            <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Open
        </a>
        <button onclick="navigator.clipboard.writeText('{{ $qrUrl }}').then(()=>this.textContent='Copied!')"
                class="btn-secondary text-xs">Copy</button>
    </article>

    {{-- Zone breakdown --}}
    @if ($byZone->isNotEmpty())
    <article class="surface-card p-6 mb-6">
        <h3 class="mb-4 text-sm font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-map-pin mr-2 text-[var(--color-brand-600)]"></i>Zone Breakdown
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-[var(--color-surface-200)]">
                <thead class="bg-[var(--color-surface-50)] text-slate-500 text-left">
                    <tr>
                        <th class="px-4 py-2">Zone</th>
                        <th class="px-4 py-2 text-center">Present</th>
                        <th class="px-4 py-2 text-center">Late</th>
                        <th class="px-4 py-2 text-center">Excused</th>
                        <th class="px-4 py-2 text-center">Absent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @foreach ($byZone as $zoneId => $rows)
                        @php
                            $zoneName = $rows->first()->zone?->name ?? 'Unknown';
                            $grouped  = $rows->pluck('total', 'attendance_status');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $zoneName }}</td>
                            <td class="px-4 py-3 text-center text-green-600">{{ $grouped->get('present', 0) }}</td>
                            <td class="px-4 py-3 text-center text-amber-500">{{ $grouped->get('late', 0) }}</td>
                            <td class="px-4 py-3 text-center text-blue-500">{{ $grouped->get('excused', 0) }}</td>
                            <td class="px-4 py-3 text-center text-red-500">{{ $grouped->get('absent', 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </article>
    @endif

    {{-- Attendance records table --}}
    <article class="surface-card p-6">
        <h3 class="mb-4 text-sm font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-users mr-2 text-[var(--color-brand-600)]"></i>Attendance Records
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-[var(--color-surface-200)]">
                <thead class="bg-[var(--color-surface-50)] text-slate-500 text-left">
                    <tr>
                        <th class="px-4 py-2">Person</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Mode</th>
                        <th class="px-4 py-2">Zone</th>
                        <th class="px-4 py-2">Check-In</th>
                        <th class="px-4 py-2">Notes</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($records as $record)
                        <tr>
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $record->person_name }}</td>
                            <td class="px-4 py-3 capitalize text-slate-500">{{ $record->person_type }}</td>
                            <td class="px-4 py-3"><x-ui.status-badge :status="$record->attendance_status" /></td>
                            <td class="px-4 py-3 capitalize text-slate-500">{{ str_replace('_', ' ', $record->attendance_mode) }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $record->zone?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $record->check_in_time?->format('H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs max-w-xs truncate">{{ $record->notes ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('attendance.record.edit', $record) }}"
                                   class="text-[var(--color-brand-600)] hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No records for this service yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $records->links() }}</div>
    </article>
    </div>
</x-layouts.app>
