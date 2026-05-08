<x-layouts.app title="Member Details">
    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <article class="surface-card p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Member profile</p>
                    <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $member->full_name }}</h3>
                </div>
                <a href="{{ route('members.edit', $member) }}" class="btn-secondary">Edit</a>
            </div>

            <dl class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ([
                    'Gender' => $member->gender,
                    'Phone' => $member->phone,
                    'Zone' => $member->zone,
                    'Residency' => $member->residency,
                    'Marital status' => $member->marital_status,
                    'Partner name' => $member->partner_name,
                    'Member ID' => $member->member_code,
                    'Tithe code' => $member->tithe_code,
                    'Username' => $member->username,
                    'Email' => $member->email,
                ] as $label => $value)
                    <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $label }}</dt>
                        <dd class="mt-2 text-sm text-[var(--color-ink-950)]">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </article>

        {{-- Right column --}}
        <div class="flex flex-col gap-6">
            <article class="surface-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Milestones</p>
                <div class="mt-3">
                    <a href="{{ route('members.timeline', $member) }}" class="btn-secondary">View full timeline</a>
                </div>
                <div class="mt-4 space-y-4 text-sm text-slate-600">
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Date of birth</p>
                        <p>{{ optional($member->date_of_birth)->format('d M Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Membership date</p>
                        <p>{{ optional($member->membership_date)->format('d M Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Born again</p>
                        <p>{{ $member->is_born_again ? 'Yes' : 'No' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Born again date</p>
                        <p>{{ optional($member->born_again_date)->format('d M Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Baptized</p>
                        <p>{{ $member->is_baptized ? 'Yes' : 'No' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Baptized date</p>
                        <p>{{ optional($member->baptized_date)->format('d M Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--color-ink-950)]">Holy Spirit baptized</p>
                        <p>{{ $member->holy_spirit_baptised ? 'Yes' : 'No' }}</p>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl bg-[var(--color-surface-50)] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Remarks</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $member->remarks ?: 'No remarks recorded.' }}</p>
                </div>
            </article>

            {{-- QR Code card --}}
            <article class="surface-card p-5 flex flex-col items-center gap-3">
                <p class="text-xs font-semibold text-[var(--color-ink-950)] self-start">
                    <i class="fa-solid fa-qrcode mr-1.5 text-[var(--color-brand-600)]"></i>Attendance QR Code
                </p>
                @if ($member->qr_token)
                    <div id="memberQrCode" class="rounded-xl overflow-hidden border border-[var(--color-surface-200)] p-2 bg-white"></div>
                    <p class="text-center text-xs text-slate-400">Scan at service entrance</p>
                    <button id="downloadMemberQr" class="btn-secondary text-xs w-full text-center">
                        <i class="fa-solid fa-download mr-1"></i>Download QR
                    </button>
                    @if ($member->phone)
                    <button id="sendMemberWhatsapp"
                            class="w-full rounded-xl border border-green-400 bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-base"></i>Send via WhatsApp
                    </button>
                    <p id="memberWaStatus" class="text-xs text-center text-slate-400 hidden"></p>
                    @else
                    <p class="text-xs text-amber-600 text-center">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>No phone — WhatsApp unavailable
                    </p>
                    @endif
                @else
                    <p class="text-sm text-slate-400 text-center">No QR token assigned yet.</p>
                @endif
            </article>
        </div>{{-- end right column --}}
    </section>

    @push('scripts')
    @if ($member->qr_token)
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById('memberQrCode'), {
            text:          '{{ $member->qr_token }}',
            width:         200,
            height:        200,
            colorDark:     '#000',
            colorLight:    '#fff',
            correctLevel:  QRCode.CorrectLevel.H,
        });

        document.getElementById('downloadMemberQr').addEventListener('click', () => {
            setTimeout(() => { // qrcodejs is async
                const canvas = document.querySelector('#memberQrCode canvas');
                const img    = document.querySelector('#memberQrCode img');
                const a = document.createElement('a');
                a.download = '{{ Str::slug($member->full_name) }}-qr.png';
                a.href = canvas ? canvas.toDataURL('image/png') : (img ? img.src : '#');
                a.click();
            }, 100);
        });

        @if ($member->phone)
        document.getElementById('sendMemberWhatsapp').addEventListener('click', function () {
            const btn = this, statusEl = document.getElementById('memberWaStatus');
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
                body: JSON.stringify({ person_type: 'member', person_id: {{ $member->id }} }),
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
