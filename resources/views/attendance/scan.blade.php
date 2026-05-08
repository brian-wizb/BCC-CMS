<x-layouts.app title="QR Attendance Scanner">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('attendance.index') }}" class="text-slate-400 hover:text-[var(--color-brand-600)] transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow">
            <i class="fa-solid fa-qrcode text-xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-semibold text-[var(--color-ink-950)]">QR Attendance Scanner</h1>
            <p class="text-sm text-slate-500">Point the camera at a member's QR code to record attendance.</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        {{-- Left: setup + camera --}}
        <div class="space-y-4">
            {{-- Service selector --}}
            <article class="surface-card p-5">
                <label class="block text-xs font-semibold text-slate-500 mb-1">
                    <i class="fa-solid fa-church mr-1 text-indigo-500"></i>Active Service <span class="text-red-500">*</span>
                </label>
                <select id="serviceSelect" class="form-input w-full">
                    <option value="">— Select the current service —</option>
                    @foreach ($services as $svc)
                        <option value="{{ $svc->id }}">
                            {{ $svc->name }} — {{ $svc->service_date?->format('d M Y') }}
                            @if ($svc->start_time) ({{ $svc->start_time }}) @endif
                        </option>
                    @endforeach
                </select>
            </article>

            {{-- Camera panel --}}
            <article class="surface-card overflow-hidden" id="cameraPanel">
                <div class="flex items-center justify-between px-5 py-3 border-b border-[var(--color-surface-200)]">
                    <p class="text-sm font-semibold text-[var(--color-ink-950)]">Camera</p>
                    <div class="flex gap-2">
                        <button id="startBtn"
                                class="btn-secondary text-xs px-3 py-1.5 border-indigo-400 text-indigo-700 hover:bg-indigo-50">
                            <i class="fa-solid fa-camera mr-1"></i>Start Camera
                        </button>
                        <button id="stopBtn"
                                class="btn-secondary text-xs px-3 py-1.5 text-red-600 hover:bg-red-50 hidden">
                            <i class="fa-solid fa-stop mr-1"></i>Stop
                        </button>
                    </div>
                </div>

                <div id="reader" class="w-full bg-gray-900" style="min-height:200px;"></div>

                {{-- Manual token fallback --}}
                <div class="px-5 py-4 border-t border-[var(--color-surface-200)]">
                    <p class="text-xs text-slate-400 mb-2">Or enter token manually:</p>
                    <div class="flex gap-2">
                        <input id="manualToken" type="text" class="form-input flex-1 text-xs" placeholder="Token from QR code">
                        <button id="manualBtn" class="btn-secondary text-xs px-3">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </article>
        </div>

        {{-- Right: scan log --}}
        <div class="space-y-4">
            <article class="surface-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-[var(--color-ink-950)]">
                        <i class="fa-solid fa-clipboard-check mr-2 text-indigo-500"></i>Today's Scan Log
                    </p>
                    <span id="scanCount" class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">0 scanned</span>
                </div>

                {{-- Last scan result --}}
                <div id="lastScan" class="mb-4 rounded-xl p-4 border border-[var(--color-surface-200)] text-center text-slate-400 text-sm hidden">
                </div>

                {{-- Log list --}}
                <ul id="scanLog" class="space-y-2 max-h-96 overflow-y-auto">
                    <li class="text-xs text-slate-400 text-center py-4">Scans will appear here.</li>
                </ul>
            </article>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const csrfToken   = '{{ csrf_token() }}';
        const scanUrl     = '{{ route('attendance.scan.record') }}';
        const serviceEl   = document.getElementById('serviceSelect');
        const startBtn    = document.getElementById('startBtn');
        const stopBtn     = document.getElementById('stopBtn');
        const logEl       = document.getElementById('scanLog');
        const lastScanEl  = document.getElementById('lastScan');
        const countEl     = document.getElementById('scanCount');
        const manualTokenEl = document.getElementById('manualToken');
        const manualBtn   = document.getElementById('manualBtn');

        let html5QrCode = null;
        let scanCount   = 0;
        let lastToken   = null;   // prevent duplicate rapid scans
        let lockout     = false;

        async function handleToken(token) {
            if (lockout || token === lastToken) return;

            const serviceId = serviceEl.value;
            if (! serviceId) {
                showResult('⚠ Please select a service first.', 'warn');
                return;
            }

            lockout   = true;
            lastToken = token;

            try {
                const resp = await fetch(scanUrl, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ token, service_id: serviceId }),
                });

                const json = await resp.json();

                if (resp.ok && json.success) {
                    scanCount++;
                    countEl.textContent = `${scanCount} scanned`;

                    const badgeColor = json.status === 'late'
                        ? 'bg-amber-100 text-amber-800 border-amber-200'
                        : 'bg-green-100 text-green-800 border-green-200';

                    const statusLabel = json.status === 'late' ? 'Late' : 'Present';

                    showResult(
                        `<div class="flex flex-col items-center gap-1">
                            <i class="fa-solid fa-circle-check text-2xl ${json.status === 'late' ? 'text-amber-500' : 'text-green-500'}"></i>
                            <p class="font-semibold text-[var(--color-ink-950)] text-base">${escHtml(json.name)}</p>
                            <span class="inline-flex items-center rounded-full border px-3 py-0.5 text-xs font-semibold ${badgeColor}">${statusLabel}</span>
                            <p class="text-xs text-slate-400">${json.time}</p>
                        </div>`,
                        'ok'
                    );

                    prependLog(json.name, json.type, statusLabel, json.time, badgeColor);
                } else {
                    showResult(`<i class="fa-solid fa-circle-xmark text-red-400 text-xl"></i><p class="mt-1 text-sm text-red-600">${escHtml(json.error ?? 'Unknown error')}</p>`, 'error');
                }
            } catch (e) {
                showResult('<p class="text-red-500 text-sm">Network error. Please retry.</p>', 'error');
            }

            // Unlock after 2 seconds to allow next scan
            setTimeout(() => { lockout = false; }, 2000);
        }

        function showResult(html, type) {
            const colors = { ok: 'bg-green-50 border-green-200', error: 'bg-red-50 border-red-200', warn: 'bg-amber-50 border-amber-200' };
            lastScanEl.className = `mb-4 rounded-xl p-4 border text-center text-sm ${colors[type] || ''}`;
            lastScanEl.innerHTML = html;
            lastScanEl.classList.remove('hidden');
        }

        function prependLog(name, type, status, time, badgeColor) {
            // Remove placeholder
            const placeholder = logEl.querySelector('.text-slate-400');
            if (placeholder) placeholder.closest('li')?.remove();

            const li = document.createElement('li');
            li.className = 'flex items-center justify-between rounded-xl border border-[var(--color-surface-200)] px-3 py-2 text-sm';
            li.innerHTML = `
                <div>
                    <p class="font-medium text-[var(--color-ink-950)]">${escHtml(name)}</p>
                    <p class="text-xs text-slate-400 capitalize">${escHtml(type)}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold ${badgeColor}">${escHtml(status)}</span>
                    <p class="text-xs text-slate-400 mt-0.5">${escHtml(time)}</p>
                </div>`;
            logEl.prepend(li);
        }

        function escHtml(str) {
            return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        // Start camera
        startBtn.addEventListener('click', () => {
            if (! serviceEl.value) {
                alert('Please select a service before starting the camera.');
                return;
            }
            html5QrCode = new Html5Qrcode('reader');
            html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                (decodedText) => handleToken(decodedText.trim()),
                () => {}
            ).then(() => {
                startBtn.classList.add('hidden');
                stopBtn.classList.remove('hidden');
            }).catch(err => alert('Camera error: ' + err));
        });

        // Stop camera
        stopBtn.addEventListener('click', () => {
            html5QrCode?.stop().then(() => {
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                lastToken = null;
            });
        });

        // Manual entry
        manualBtn.addEventListener('click', () => {
            const token = manualTokenEl.value.trim();
            if (token) { handleToken(token); manualTokenEl.value = ''; }
        });
        manualTokenEl.addEventListener('keydown', e => {
            if (e.key === 'Enter') manualBtn.click();
        });
    </script>
    @endpush
</x-layouts.app>
