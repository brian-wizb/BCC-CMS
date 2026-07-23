<x-layouts.app title="Member Details">
    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <article class="surface-card p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('members.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-[var(--color-surface-100)] transition" title="Back to members">
                        <i class="fas fa-arrow-left text-slate-500 text-sm"></i>
                    </a>
                    <div class="h-16 w-16 overflow-hidden rounded-2xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)]">
                        @if ($member->profile_pic)
                            <img
                                src="{{ route('members.profile-picture', $member) }}"
                                alt="{{ $member->full_name }} profile picture"
                                class="h-full w-full object-cover"
                                onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($member->full_name ?: 'Member') }}&background=e2e8f0&color=475569&size=256';"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center text-slate-400">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Member profile</p>
                        <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">{{ $member->full_name }}</h3>
                    </div>
                </div>
                <a href="{{ route('members.edit', $member) }}" class="btn-secondary">Edit</a>
            </div>

            <dl class="mt-6 grid gap-4 md:grid-cols-2">
                {{-- Gender --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-venus-mars text-sm opacity-50"></i>Gender
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->gender ?: '—' }}</dd>
                </div>

                {{-- Phone --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-phone text-sm opacity-50"></i>Phone
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->phone ?: '—' }}</dd>
                </div>

                {{-- Zone --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-map-marker-alt text-sm opacity-50"></i>Zone
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->zone ?: '—' }}</dd>
                </div>

                {{-- Residency --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-home text-sm opacity-50"></i>Residency
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->residency ?: '—' }}</dd>
                </div>

                {{-- Marital Status --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-ring text-sm opacity-50"></i>Marital status
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->marital_status ?: '—' }}</dd>
                </div>

                {{-- Partner --}}
                <div class="rounded-2xl border" style="border-color: {{ $member->partner_member_id ? 'rgba(59,130,246,0.3)' : 'var(--color-surface-200)' }}; background-color: {{ $member->partner_member_id ? 'rgba(59,130,246,0.05)' : 'transparent' }};">
                    <div class="p-4">
                        <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em]" style="color: {{ $member->partner_member_id ? 'rgba(59,130,246,0.9)' : 'var(--color-slate-400)' }};">
                            <i class="fas fa-heart text-sm opacity-60"></i>Partner
                        </dt>
                        <dd class="mt-2 text-sm font-medium">
                            @if ($member->partner_member_id)
                                <a href="{{ route('members.show', $member->partnerMember) }}" class="text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center gap-1.5">
                                    {{ $member->partnerMember?->full_name }}
                                    <i class="fas fa-external-link-alt text-xs opacity-60"></i>
                                </a>
                                @if ($member->share_partner_tithe_code)
                                    <span class="mt-1 inline-block rounded-full bg-green-100 px-2 py-1 text-xs text-green-700 ml-2">
                                        <i class="fas fa-link text-xs mr-1"></i>Tithe linked
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-600">{{ $member->partner_name ?: '—' }}</span>
                            @endif
                        </dd>
                    </div>
                </div>

                {{-- Member ID --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-id-card text-sm opacity-50"></i>Member ID
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->member_code ?: '—' }}</dd>
                </div>

                {{-- Tithe Code --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-hand-holding-usd text-sm opacity-50"></i>Tithe code
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->tithe_code ?: '—' }}</dd>
                </div>

                {{-- Username --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-at text-sm opacity-50"></i>Username
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">{{ $member->username ?: '—' }}</dd>
                </div>

                {{-- Email --}}
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4 hover:border-[var(--color-brand-300)] transition">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-envelope text-sm opacity-50"></i>Email
                    </dt>
                    <dd class="mt-2 text-sm text-[var(--color-ink-950)] font-medium">
                        @if ($member->email)
                            <a href="mailto:{{ $member->email }}" class="text-blue-600 hover:underline">{{ $member->email }}</a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
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

                {{-- Employment & Education --}}
                <div class="mt-6 rounded-2xl bg-[var(--color-surface-50)] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <i class="fas fa-briefcase mr-1.5 opacity-60" style="color:rgba(16,185,129,0.8);"></i>Employment &amp; Education
                    </p>
                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Employment</span>
                            <span class="font-medium text-[var(--color-ink-950)]">{{ $member->employment_status ?: '—' }}</span>
                        </div>
                        @if ($member->is_university_student)
                            <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                                <p class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 mb-2">
                                    <i class="fas fa-graduation-cap"></i> University Student
                                </p>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">University</span>
                                        <span class="font-medium text-[var(--color-ink-950)]">
                                            {{ $member->university?->name ?: '—' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">Enrolled</span>
                                        <span class="font-medium">{{ optional($member->university_start_date)->format('d M Y') ?: '—' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">Graduates</span>
                                        <span class="font-medium">{{ optional($member->university_end_date)->format('d M Y') ?: '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
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
            width:         420,
            height:        420,
            colorDark:     '#000',
            colorLight:    '#fff',
            correctLevel:  QRCode.CorrectLevel.M,
        });

        setTimeout(() => {
            const canvas = document.querySelector('#memberQrCode canvas');
            const img = document.querySelector('#memberQrCode img');
            if (canvas) {
                canvas.style.width = '200px';
                canvas.style.height = '200px';
                canvas.style.imageRendering = 'pixelated';
            }
            if (img) {
                img.style.width = '200px';
                img.style.height = '200px';
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

        document.getElementById('downloadMemberQr').addEventListener('click', () => {
            setTimeout(() => { // qrcodejs is async
                const canvas = document.querySelector('#memberQrCode canvas');
                const img    = document.querySelector('#memberQrCode img');
                const a = document.createElement('a');
                a.download = '{{ Str::slug($member->full_name) }}-qr.png';
                a.href = canvas
                    ? makePaddedQrDataUrl(canvas)
                    : (img ? img.src : '#');
                a.click();
            }, 140);
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
