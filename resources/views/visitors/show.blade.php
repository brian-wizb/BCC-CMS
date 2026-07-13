<x-layouts.app title="Visitor Details">
    <section class="space-y-6">

        {{-- ── Page header ─────────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                    <i class="fa-solid fa-users mr-1"></i> Visitors
                </p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">
                    <i class="fa-solid fa-user mr-2" style="color:#2563eb;"></i> {{ $visitor->full_name }}
                </h3>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('visitors.edit', $visitor) }}" class="btn-secondary">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                </a>
                <a href="{{ route('visitors.index') }}" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">

            {{-- ── LEFT: detail + follow-up tasks ─────────────────────── --}}
            <div class="space-y-6">

                {{-- Detail card --}}
                <article class="surface-card p-6">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">
                            <i class="fa-solid fa-address-card w-4 text-center"></i> Visitor Details
                        </h4>
                        <x-ui.status-badge :status="$visitor->status" />
                    </div>

                    <dl class="mt-5 grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-phone mr-1"></i> Phone
                            </dt>
                            <dd class="mt-2 font-medium">{{ $visitor->phone ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-envelope mr-1"></i> Email
                            </dt>
                            <dd class="mt-2 font-medium">{{ $visitor->email ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-venus-mars mr-1"></i> Gender
                            </dt>
                            <dd class="mt-2 font-medium">{{ ucfirst($visitor->gender ?: '—') }}</dd>
                        </div>
                        <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-location-dot mr-1"></i> Address
                            </dt>
                            <dd class="mt-2 font-medium">{{ $visitor->address ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-calendar-day mr-1"></i> First Visit
                            </dt>
                            <dd class="mt-2 font-medium">{{ optional($visitor->first_visit_date)->format('d M Y') ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-church mr-1"></i> Service
                            </dt>
                            <dd class="mt-2 font-medium">{{ $visitor->service?->name ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 md:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-user-group mr-1"></i> Invited By
                            </dt>
                            <dd class="mt-2 font-medium">{{ $visitor->invited_by ?: '—' }}</dd>
                        </div>
                    </dl>

                    @if ($visitor->notes)
                        <div class="mt-4 rounded-2xl bg-[var(--color-surface-50)] p-4 text-sm text-slate-600">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                <i class="fa-solid fa-comment-alt mr-1"></i> Notes
                            </p>
                            {{ $visitor->notes }}
                        </div>
                    @endif
                </article>

                {{-- Follow-up tasks panel --}}
                <article class="surface-card p-6">
                    <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-emerald-600">
                        <i class="fa-solid fa-clipboard-list w-4 text-center"></i>
                        Follow-up Tasks ({{ $visitor->followUpTasks->count() }})
                    </h4>

                    @forelse ($visitor->followUpTasks as $task)
                        <div class="mb-3 rounded-2xl border border-[var(--color-surface-200)] p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold uppercase
                                        @if ($task->priority === 'high') bg-red-100 text-red-700
                                        @elseif ($task->priority === 'medium') bg-amber-100 text-amber-700
                                        @else bg-slate-100 text-slate-600 @endif">
                                        {{ $task->priority }}
                                    </span>
                                    <span class="text-sm font-semibold text-[var(--color-ink-950)]">
                                        {{ strtoupper($task->task_type) }}
                                    </span>
                                </div>
                                <x-ui.status-badge :status="$task->status" />
                            </div>
                            <p class="mt-2 flex items-center gap-1 text-xs text-slate-500">
                                <i class="fa-solid fa-user-shield"></i>
                                {{ $task->leader?->full_name ?: 'Unassigned' }}
                                @if ($task->due_date)
                                    &nbsp;·&nbsp;<i class="fa-solid fa-calendar"></i>
                                    Due {{ $task->due_date->format('d M Y') }}
                                @endif
                            </p>
                            @if ($task->notes)
                                <p class="mt-1 text-xs text-slate-500">{{ $task->notes }}</p>
                            @endif
                            {{-- Recent history --}}
                            @foreach ($task->history->take(2) as $entry)
                                <p class="mt-1 text-xs text-slate-400">
                                    <i class="fa-solid fa-clock-rotate-left mr-1"></i>
                                    {{ $entry->created_at?->format('d M Y H:i') }}: {{ $entry->action_taken }}
                                </p>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No follow-up tasks assigned yet.</p>
                    @endforelse

                    <a href="{{ route('follow-up.tasks') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:underline">
                        <i class="fa-solid fa-plus"></i> Add task in Follow-up module
                    </a>
                </article>
            </div>

            {{-- ── RIGHT: actions sidebar ───────────────────────────────── --}}
            <aside class="space-y-4">
                {{-- Status update --}}
                <div class="surface-card p-6">
                    <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">
                        <i class="fa-solid fa-arrows-rotate w-4 text-center"></i> Update Status
                    </h4>
                    <form method="POST" action="{{ route('visitors.status.update', $visitor) }}">
                        @csrf
                        @method('PATCH')
                        <select id="status" name="status" class="form-input">
                            @foreach (['new' => 'New', 'contacted' => 'Contacted', 'counseled' => 'Counseled', 'joined_zone' => 'Joined Zone', 'in_class' => 'In Class', 'converted' => 'Converted'] as $val => $label)
                                <option value="{{ $val }}" @selected($visitor->status === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn-secondary mt-3 w-full" type="submit">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Save status
                        </button>
                    </form>
                </div>

                {{-- Convert to member --}}
                <div class="surface-card p-6">
                    <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-emerald-600">
                        <i class="fa-solid fa-user-check w-4 text-center"></i> Conversion
                    </h4>
                    @if ($visitor->convertedMember)
                        <p class="text-sm text-slate-500">
                            Converted to member:
                            <a href="{{ route('members.show', $visitor->convertedMember) }}"
                               class="font-semibold text-blue-600 hover:underline">
                                {{ $visitor->convertedMember->full_name }}
                            </a>
                        </p>
                    @else
                        <form method="POST" action="{{ route('visitors.convert', $visitor) }}"
                              data-confirm="Convert this visitor into a member?">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-primary w-full">
                                <i class="fa-solid fa-user-plus mr-1"></i> Convert to member
                            </button>
                        </form>
                    @endif
                </div>

                {{-- QR Code --}}
                <div class="surface-card p-5 flex flex-col items-center gap-3">
                    <p class="text-xs font-semibold text-[var(--color-ink-950)] self-start">
                        <i class="fa-solid fa-qrcode mr-1.5 text-[var(--color-brand-600)]"></i>Attendance QR Code
                    </p>
                    @if ($visitor->qr_token)
                        <div id="visitorQrCode" class="rounded-xl overflow-hidden border border-[var(--color-surface-200)] p-2 bg-white"></div>
                        <p class="text-center text-xs text-slate-400">Scan at service entrance</p>
                        <button id="downloadVisitorQr" class="btn-secondary text-xs w-full text-center">
                            <i class="fa-solid fa-download mr-1"></i>Download QR
                        </button>
                        @if ($visitor->phone)
                        <button id="sendVisitorWhatsapp"
                                class="w-full rounded-xl border border-green-400 bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors flex items-center justify-center gap-1.5">
                            <i class="fa-brands fa-whatsapp text-base"></i>Send via WhatsApp
                        </button>
                        <p id="visitorWaStatus" class="text-xs text-center text-slate-400 hidden"></p>
                        @else
                        <p class="text-xs text-amber-600 text-center">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>No phone — WhatsApp unavailable
                        </p>
                        @endif
                    @else
                        <p class="text-sm text-slate-400 text-center">No QR token assigned yet.</p>
                    @endif
                </div>

                {{-- Delete --}}
                <div class="surface-card p-6">
                    <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-red-400">
                        <i class="fa-solid fa-trash w-4 text-center"></i> Danger Zone
                    </h4>
                    <form method="POST" action="{{ route('visitors.destroy', $visitor) }}"
                          data-confirm="Permanently delete {{ $visitor->full_name }}?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100">
                            <i class="fa-solid fa-trash mr-1"></i> Delete visitor
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </section>

    @push('scripts')
    @if ($visitor->qr_token)
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById('visitorQrCode'), {
            text:         '{{ $visitor->qr_token }}',
            width:        420,
            height:       420,
            colorDark:    '#000',
            colorLight:   '#fff',
            correctLevel: QRCode.CorrectLevel.M,
        });

        setTimeout(() => {
            const canvas = document.querySelector('#visitorQrCode canvas');
            const img = document.querySelector('#visitorQrCode img');
            if (canvas) {
                canvas.style.width = '180px';
                canvas.style.height = '180px';
                canvas.style.imageRendering = 'pixelated';
            }
            if (img) {
                img.style.width = '180px';
                img.style.height = '180px';
                img.style.imageRendering = 'pixelated';
            }
        }, 120);

        function makePaddedQrDataUrl(canvas, padding = 56) {
            const out = document.createElement('canvas');
            out.width = canvas.width + (padding * 2);
            out.height = canvas.height + (padding * 2);

            const ctx = out.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, out.width, out.height);
            ctx.drawImage(canvas, padding, padding);

            return out.toDataURL('image/png');
        }

        document.getElementById('downloadVisitorQr').addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.querySelector('#visitorQrCode canvas');
                const img    = document.querySelector('#visitorQrCode img');
                const a = document.createElement('a');
                a.download = '{{ Str::slug($visitor->full_name) }}-qr.png';
                a.href = canvas
                    ? makePaddedQrDataUrl(canvas)
                    : (img ? img.src : '#');
                a.click();
            }, 140);
        });

        @if ($visitor->phone)
        document.getElementById('sendVisitorWhatsapp').addEventListener('click', function () {
            const btn = this, statusEl = document.getElementById('visitorWaStatus');
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
                body: JSON.stringify({ person_type: 'visitor', person_id: {{ $visitor->id }} }),
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
