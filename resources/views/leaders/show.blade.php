<x-layouts.app title="Leader Profile">
    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_300px]">

        {{-- LEFT --}}
        <div class="space-y-5">
            <article class="surface-card p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl text-lg font-bold"
                              style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">
                            {{ mb_strtoupper(mb_substr($leader->full_name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Leader</p>
                            <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">{{ $leader->full_name }}</h3>
                            @if ($leader->role)
                                <p class="mt-0.5 text-sm text-slate-500">{{ $leader->role }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('leaders.edit', $leader) }}"
                           class="btn-secondary flex items-center gap-1.5 text-sm">
                            <i class="fas fa-pen text-xs"></i> Edit
                        </a>
                        <a href="{{ route('leaders.index') }}"
                           class="btn-secondary flex items-center gap-1.5 text-sm">
                            <i class="fas fa-arrow-left text-xs"></i> Back
                        </a>
                    </div>
                </div>

                <dl class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @php
                        $details = [
                            ['fa-phone',          'Phone',  $leader->phone],
                            ['fa-envelope',       'Email',  $leader->email],
                            ['fa-map-marker-alt', 'Zone',   $leader->zone],
                            ['fa-id-card',        'Member', $leader->member?->full_name],
                            ['fa-toggle-on',      'Status', ucfirst($leader->status)],
                            ['fa-clock',          'Added',  $leader->created_at?->format('d M Y')],
                        ];
                    @endphp
                    @foreach ($details as [$icon, $label, $value])
                        <div class="rounded-xl border border-[var(--color-surface-200)] px-4 py-3">
                            <dt class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                                <i class="fas {{ $icon }} opacity-60"></i> {{ $label }}
                            </dt>
                            <dd class="mt-1.5 text-sm text-[var(--color-ink-950)]">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($leader->notes)
                    <div class="mt-4 rounded-xl bg-[var(--color-surface-50)] px-4 py-3">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                            <i class="fas fa-comment-alt mr-1 opacity-60"></i> Notes
                        </p>
                        <p class="text-sm leading-6 text-slate-600">{{ $leader->notes }}</p>
                    </div>
                @endif
            </article>

            {{-- Assigned follow-up tasks --}}
            <article class="surface-card overflow-hidden">
                <div class="flex items-center gap-2 border-b border-[var(--color-surface-200)] px-6 py-4">
                    <i class="fas fa-tasks text-sm" style="color:rgba(167,139,250,0.9);"></i>
                    <h4 class="font-semibold text-[var(--color-ink-950)]">Assigned follow-up tasks</h4>
                    <span class="ml-0.5 rounded-full px-1.5 py-0.5 text-xs font-semibold"
                          style="background:rgba(167,139,250,0.12); color:rgba(167,139,250,0.9);">
                        {{ $leader->followUpTasks->count() }}
                    </span>
                </div>
                @if ($leader->followUpTasks->isEmpty())
                    <p class="px-6 py-6 text-sm text-slate-400">No tasks assigned to this leader.</p>
                @else
                    <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                        <thead class="bg-[var(--color-surface-50)] text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">
                            <tr>
                                <th class="px-5 py-3 text-left"><i class="fas fa-tasks mr-1.5 opacity-60"></i>Task</th>
                                <th class="px-5 py-3 text-left"><i class="fas fa-user mr-1.5 opacity-60"></i>Person</th>
                                <th class="px-5 py-3 text-left"><i class="fas fa-flag mr-1.5 opacity-60"></i>Priority</th>
                                <th class="px-5 py-3 text-left"><i class="fas fa-info-circle mr-1.5 opacity-60"></i>Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @foreach ($leader->followUpTasks as $task)
                                <tr class="hover:bg-[var(--color-surface-50)] transition">
                                    <td class="px-5 py-3 font-medium text-[var(--color-ink-950)]">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ ucfirst($task->person_type) }} #{{ $task->person_id }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ ucfirst($task->priority) }}</td>
                                    <td class="px-5 py-3"><x-ui.status-badge :status="$task->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </article>
        </div>

        {{-- RIGHT: actions --}}
        <div class="space-y-4">
            <article class="surface-card p-5">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    <i class="fas fa-cog mr-1 opacity-60"></i> Actions
                </p>
                <form method="POST" action="{{ route('leaders.destroy', $leader) }}"
                      onsubmit="return confirm('Delete this leader permanently?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn-secondary flex w-full items-center justify-center gap-2 text-red-500 hover:text-red-600">
                        <i class="fas fa-trash text-xs"></i> Delete leader
                    </button>
                </form>
            </article>

            {{-- QR Code card --}}
            <article class="surface-card p-5 flex flex-col items-center gap-3">
                <p class="text-xs font-semibold text-[var(--color-ink-950)] self-start">
                    <i class="fa-solid fa-qrcode mr-1.5 text-[var(--color-brand-600)]"></i>Attendance QR Code
                </p>
                @if ($leader->qr_token)
                    <div id="leaderQrCode" class="rounded-xl overflow-hidden border border-[var(--color-surface-200)] p-2 bg-white"></div>
                    <p class="text-center text-xs text-slate-400">Scan at service entrance</p>
                    <button id="downloadLeaderQr" class="btn-secondary text-xs w-full text-center">
                        <i class="fa-solid fa-download mr-1"></i>Download QR
                    </button>
                    @if ($leader->phone)
                    <button id="sendLeaderWhatsapp"
                            class="w-full rounded-xl border border-green-400 bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-base"></i>Send via WhatsApp
                    </button>
                    <p id="leaderWaStatus" class="text-xs text-center text-slate-400 hidden"></p>
                    @else
                    <p class="text-xs text-amber-600 text-center">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>No phone — WhatsApp unavailable
                    </p>
                    @endif
                @else
                    <p class="text-sm text-slate-400 text-center">No QR token assigned yet.</p>
                @endif
            </article>
        </div>

    </section>

    @push('scripts')
    @if ($leader->qr_token)
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById('leaderQrCode'), {
            text:         '{{ $leader->qr_token }}',
            width:        180,
            height:       180,
            colorDark:    '#000',
            colorLight:   '#fff',
            correctLevel: QRCode.CorrectLevel.H,
        });

        document.getElementById('downloadLeaderQr').addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.querySelector('#leaderQrCode canvas');
                const img    = document.querySelector('#leaderQrCode img');
                const a = document.createElement('a');
                a.download = '{{ Str::slug($leader->full_name) }}-qr.png';
                a.href = canvas ? canvas.toDataURL('image/png') : (img ? img.src : '#');
                a.click();
            }, 100);
        });

        @if ($leader->phone)
        document.getElementById('sendLeaderWhatsapp').addEventListener('click', function () {
            const btn = this, statusEl = document.getElementById('leaderWaStatus');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending…';
            statusEl.classList.add('hidden');

            fetch('{{ route("attendance.qr.send") }}', {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ person_type: 'leader', person_id: {{ $leader->id }} }),
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
    </script>
    @endif
    @endpush
</x-layouts.app>
