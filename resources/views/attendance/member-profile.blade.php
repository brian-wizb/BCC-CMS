<x-layouts.app :title="'Attendance: '.$member->full_name">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('attendance.reports') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[var(--color-brand-600)] text-white shadow">
                <i class="fa-solid fa-user text-xl"></i>
            </span>
            <div>
                <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">{{ $member->full_name }}</h1>
                <p class="text-sm text-slate-500">Attendance profile</p>
            </div>
        </div>
        <a href="{{ route('members.show', $member) }}" class="btn-secondary text-sm">
            <i class="fa-solid fa-id-card mr-1.5"></i>Member Profile
        </a>
    </div>

    {{-- Top row: stats + QR side by side --}}
    <div class="mb-6 grid gap-6 md:grid-cols-3">
        {{-- Stats (2/3 width) --}}
        <div class="md:col-span-2 space-y-4">
            <div class="grid gap-4 grid-cols-2 sm:grid-cols-4">
                <article class="stat-card text-center">
                    <p class="text-xs text-slate-500">Total Services</p>
                    <p class="mt-1 text-3xl font-semibold text-[var(--color-ink-950)]">{{ $totalServices }}</p>
                </article>
                <article class="stat-card text-center">
                    <p class="text-xs text-slate-500">Times Present</p>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ $attended }}</p>
                </article>
                <article class="stat-card text-center">
                    <p class="text-xs text-slate-500">Attendance Rate</p>
                    <p class="mt-1 text-3xl font-semibold
                        @if($rate >= 75) text-green-600
                        @elseif($rate >= 50) text-amber-500
                        @else text-red-500 @endif">
                        {{ $rate }}%
                    </p>
                </article>
                <article class="stat-card text-center">
                    <p class="text-xs text-slate-500">Streak</p>
                    <p class="mt-1 text-3xl font-semibold text-[var(--color-brand-600)]">{{ $streak }}</p>
                    <p class="text-xs text-slate-400">services</p>
                </article>
            </div>

            {{-- Rate bar --}}
            <article class="surface-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-slate-500 font-medium">Attendance Rate</p>
                    <p class="text-xs font-semibold text-[var(--color-ink-950)]">{{ $rate }}%</p>
                </div>
                <div class="h-3 w-full rounded-full bg-[var(--color-surface-200)] overflow-hidden">
                    <div class="h-3 rounded-full transition-all
                        @if($rate >= 75) bg-green-500
                        @elseif($rate >= 50) bg-amber-400
                        @else bg-red-400 @endif"
                        style="width: {{ $rate }}%"></div>
                </div>
            </article>

            {{-- Trend chart --}}
            @if ($trendData->isNotEmpty())
            <article class="surface-card p-5">
                <p class="mb-3 text-xs font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-chart-line mr-1.5 text-[var(--color-brand-600)]"></i>Attendance Trend (last 12 months)
                </p>
                <canvas id="trendChart" height="100"></canvas>
            </article>
            @endif
        </div>

        {{-- QR Code card (1/3 width) --}}
        <article class="surface-card p-5 flex flex-col items-center gap-3">
            <p class="text-xs font-semibold text-[var(--color-ink-950)]">
                <i class="fa-solid fa-qrcode mr-1.5 text-[var(--color-brand-600)]"></i>Personal QR Code
            </p>
            @if ($member->qr_token)
                <div id="qrCanvas" class="rounded-xl overflow-hidden border border-[var(--color-surface-200)] p-2 bg-white"></div>
                <p class="text-center text-xs text-slate-400">Scan at service entrance</p>
                <button id="downloadQr"
                        class="btn-secondary text-xs w-full text-center">
                    <i class="fa-solid fa-download mr-1"></i>Download QR
                </button>
                @if ($member->phone)
                <button id="sendWhatsapp"
                        data-person-type="member"
                        data-person-id="{{ $member->id }}"
                        class="w-full rounded-xl border border-green-400 bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-base"></i>Send via WhatsApp
                </button>
                <p id="waStatus" class="text-xs text-center text-slate-400 hidden"></p>
                @else
                <p class="text-xs text-amber-600 text-center">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>No phone number — WhatsApp not available
                </p>
                @endif
                <p class="text-[10px] text-slate-300 text-center break-all font-mono">{{ $member->full_name }}</p>
            @else
                <p class="text-sm text-slate-400 text-center">QR code not yet generated.</p>
            @endif
        </article>
    </div>

    {{-- History table --}}
    <article class="surface-card p-6">
        <h3 class="mb-4 text-sm font-semibold text-[var(--color-ink-950)]">
            <i class="fa-solid fa-calendar-check mr-2 text-[var(--color-brand-600)]"></i>Attendance History
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-2">Service</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Mode</th>
                        <th class="px-4 py-2">Check-In</th>
                        <th class="px-4 py-2">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($records as $record)
                        <tr class="hover:bg-[var(--color-surface-50)]">
                            <td class="px-4 py-3 font-medium text-[var(--color-ink-950)]">{{ $record->service?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $record->service?->service_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3"><x-ui.status-badge :status="$record->attendance_status" /></td>
                            <td class="px-4 py-3 capitalize text-slate-500">{{ str_replace('_', ' ', $record->attendance_mode) }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $record->check_in_time?->format('H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs max-w-xs truncate">{{ $record->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No attendance records found for this member.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $records->links() }}</div>
    </article>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script>
        // ── QR Code ─────────────────────────────────────────────────────
        @if ($member->qr_token)
        const qr = new QRCode(document.getElementById('qrCanvas'), {
            text:          '{{ $member->qr_token }}',
            width:         180,
            height:        180,
            colorDark:     '#1e1e2e',
            colorLight:    '#ffffff',
            correctLevel:  QRCode.CorrectLevel.H,
        });

        document.getElementById('downloadQr').addEventListener('click', () => {
            const img = document.querySelector('#qrCanvas img') || document.querySelector('#qrCanvas canvas');
            const canvas = img.tagName === 'CANVAS' ? img : null;
            if (canvas) {
                const a = document.createElement('a');
                a.download = '{{ Str::slug($member->full_name) }}-qr.png';
                a.href = canvas.toDataURL('image/png');
                a.click();
            } else if (img) {
                const a = document.createElement('a');
                a.download = '{{ Str::slug($member->full_name) }}-qr.png';
                a.href = img.src;
                a.click();
            }
        });

        @if ($member->phone)
        document.getElementById('sendWhatsapp').addEventListener('click', function () {
            const btn = this;
            const statusEl = document.getElementById('waStatus');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending…';
            statusEl.classList.add('hidden');

            fetch('{{ route("attendance.qr.send") }}', {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN':  '{{ csrf_token() }}',
                    'Content-Type':  'application/json',
                    'Accept':        'application/json',
                },
                body: JSON.stringify({
                    person_type: 'member',
                    person_id:   {{ $member->id }},
                }),
            })
            .then(r => r.json())
            .then(data => {
                statusEl.classList.remove('hidden');
                if (data.success) {
                    statusEl.className = 'text-xs text-center text-green-600';
                    statusEl.textContent = '✓ ' + data.message;
                } else {
                    statusEl.className = 'text-xs text-center text-red-500';
                    statusEl.textContent = '✗ ' + (data.error ?? 'Failed to send');
                }
            })
            .catch(() => {
                statusEl.classList.remove('hidden');
                statusEl.className = 'text-xs text-center text-red-500';
                statusEl.textContent = '✗ Network error. Please try again.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-brands fa-whatsapp text-base"></i> Send via WhatsApp';
            });
        });
        @endif
        @endif

        // ── Trend Chart ──────────────────────────────────────────────────
        @if ($trendData->isNotEmpty())
        const trendLabels  = {!! $trendData->pluck('month')->map(fn($m) => "'" . \Carbon\Carbon::parse($m.'-01')->format('M Y') . "'")->implode(',') !!};
        const trendAttended = [{{ $trendData->pluck('attended')->implode(',') }}];
        const trendTotal    = [{{ $trendData->pluck('total_recorded')->implode(',') }}];

        new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels:   trendLabels,
                datasets: [
                    {
                        label:           'Attended',
                        data:            trendAttended,
                        backgroundColor: 'rgba(99,102,241,0.7)',
                        borderRadius:    4,
                    },
                    {
                        label:           'Recorded',
                        data:            trendTotal,
                        backgroundColor: 'rgba(203,213,225,0.5)',
                        borderRadius:    4,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales:  { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            },
        });
        @endif
    </script>
    @endpush
</x-layouts.app>
