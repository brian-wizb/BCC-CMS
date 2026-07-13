<x-layouts.app title="QR Attendance Scanner">
    <div class="attendance-responsive">
    <div class="mb-6 flex items-center gap-4 attendance-header">
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
                    <div class="flex gap-2 scan-controls">
                        <button id="startBtn"
                                class="btn-secondary text-xs px-3 py-1.5 border-indigo-400 text-indigo-700 hover:bg-indigo-50">
                            <i class="fa-solid fa-camera mr-1"></i>Start Camera
                        </button>
                        <button id="uploadQrBtn"
                                class="btn-secondary text-xs px-3 py-1.5 border-[var(--color-surface-300)] text-[var(--color-ink-950)] hover:bg-[var(--color-surface-100)]">
                            <i class="fa-solid fa-image mr-1"></i>Scan QR Image
                        </button>
                        <button id="stopBtn"
                                class="btn-secondary text-xs px-3 py-1.5 text-red-600 hover:bg-red-50 hidden">
                            <i class="fa-solid fa-stop mr-1"></i>Stop
                        </button>
                    </div>
                </div>

                <div id="reader" class="w-full bg-gray-900" style="min-height:200px;"></div>
                <input id="qrImageInput" type="file" accept="image/*" capture="environment" class="hidden">

                {{-- Manual token fallback --}}
                <div class="px-5 py-4 border-t border-[var(--color-surface-200)]">
                    <p class="text-xs text-slate-400 mb-2">Or enter token manually:</p>
                    <div class="flex gap-2 scan-manual">
                        <input id="manualToken" type="text" class="form-input flex-1 text-xs" placeholder="Token from QR code">
                        <button id="manualBtn" class="btn-secondary text-xs px-3">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </article>

            <article class="surface-card p-5">
                <div class="mb-2">
                    <p class="text-sm font-semibold text-[var(--color-ink-950)]">
                        <i class="fa-solid fa-user-check mr-2 text-indigo-500"></i>Quick Record by Name
                    </p>
                    <p class="text-xs text-slate-500">Search members and visitors, then mark attendance instantly.</p>
                </div>

                <div class="flex gap-2 scan-manual">
                    <input id="personSearchInput" type="text" class="form-input flex-1 text-xs" placeholder="Type at least 2 letters of name">
                    <button id="personSearchBtn" class="btn-secondary text-xs px-3" type="button">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                <ul id="personSearchResults" class="mt-3 space-y-2 max-h-64 overflow-y-auto">
                    <li class="text-xs text-slate-400 text-center py-2">Search results will appear here.</li>
                </ul>
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
        const scanSearchPeopleUrl = '{{ route('attendance.scan.search-people') }}';
        const scanRecordPersonUrl = '{{ route('attendance.scan.record-person') }}';
        const serviceEl   = document.getElementById('serviceSelect');
        const startBtn    = document.getElementById('startBtn');
        const stopBtn     = document.getElementById('stopBtn');
        const uploadQrBtn = document.getElementById('uploadQrBtn');
        const qrImageInput = document.getElementById('qrImageInput');
        const logEl       = document.getElementById('scanLog');
        const lastScanEl  = document.getElementById('lastScan');
        const countEl     = document.getElementById('scanCount');
        const manualTokenEl = document.getElementById('manualToken');
        const manualBtn   = document.getElementById('manualBtn');
        const personSearchInput = document.getElementById('personSearchInput');
        const personSearchBtn = document.getElementById('personSearchBtn');
        const personSearchResultsEl = document.getElementById('personSearchResults');

        let html5QrCode = null;
        let scanCount   = 0;
        let lastToken   = null;   // prevent duplicate rapid scans
        let lockout     = false;
        let searchDebounceTimer = null;

        function formatCameraError(error) {
            const text = String(error?.message || error || '').toLowerCase();

            if (!window.isSecureContext) {
                return 'Live camera needs HTTPS on most mobile browsers. Use Scan QR Image below, or open this app via HTTPS/localhost.';
            }
            if (text.includes('notallowed') || text.includes('permission')) {
                return 'Camera permission denied. Please allow camera access and retry.';
            }
            if (text.includes('notfound') || text.includes('no cameras')) {
                return 'No camera was found on this device.';
            }
            if (text.includes('notreadable') || text.includes('trackstart')) {
                return 'Camera is in use by another app. Close other camera apps and retry.';
            }
            if (text.includes('notsupported') || text.includes('streaming')) {
                return 'Camera streaming is not supported in this browser context.';
            }

            return 'Unable to start camera. Try another browser or use manual token entry.';
        }

        async function startScanner() {
            if (! serviceEl.value) {
                window.showToast('warning', 'Please select a service before starting the camera.');
                return;
            }

            if (!window.isSecureContext) {
                const msg = 'Camera requires HTTPS on mobile browsers. Use HTTPS URL, then retry.';
                showResult(`<p class="text-red-500 text-sm">${msg}</p>`, 'error');
                window.showToast('error', msg);
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                const msg = 'This browser does not support camera streaming for QR scanning.';
                showResult(`<p class="text-red-500 text-sm">${msg}</p>`, 'error');
                window.showToast('error', msg);
                return;
            }

            html5QrCode = new Html5Qrcode('reader');
            const config = { fps: 10, qrbox: { width: 240, height: 240 } };

            const onScan = (decodedText) => handleToken(decodedText.trim());
            const onError = () => {};

            try {
                await html5QrCode.start({ facingMode: { exact: 'environment' } }, config, onScan, onError);
            } catch (_) {
                try {
                    await html5QrCode.start({ facingMode: 'environment' }, config, onScan, onError);
                } catch (_) {
                    try {
                        const cameras = await Html5Qrcode.getCameras();
                        if (!cameras || cameras.length === 0) {
                            throw new Error('No cameras found');
                        }

                        // Prefer back/environment labeled cameras if available.
                        const preferred = cameras.find((cam) => /back|rear|environment/i.test(cam.label)) || cameras[0];
                        await html5QrCode.start(preferred.id, config, onScan, onError);
                    } catch (finalErr) {
                        const msg = formatCameraError(finalErr);
                        showResult(`<p class="text-red-500 text-sm">${msg}</p>`, 'error');
                        window.showToast('error', msg);
                        try {
                            await html5QrCode.clear();
                        } catch {
                            // no-op
                        }
                        html5QrCode = null;
                        return;
                    }
                }
            }

            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
        }

        async function scanFromImageFile(file) {
            if (!file) {
                return;
            }

            if (! serviceEl.value) {
                window.showToast('warning', 'Please select a service before scanning.');
                return;
            }

            try {
                if (html5QrCode) {
                    await html5QrCode.stop().catch(() => {});
                    await html5QrCode.clear().catch(() => {});
                    html5QrCode = null;
                    startBtn.classList.remove('hidden');
                    stopBtn.classList.add('hidden');
                }

                const imageScanner = new Html5Qrcode('reader');
                const decodedText = await imageScanner.scanFile(file, true);
                await imageScanner.clear().catch(() => {});

                if (!decodedText) {
                    throw new Error('No QR code detected in selected image.');
                }

                await handleToken(String(decodedText).trim());
            } catch (error) {
                const msg = String(error?.message || error || 'Unable to scan the selected image.');
                showResult(`<p class="text-red-500 text-sm">${escHtml(msg)}</p>`, 'error');
                window.showToast('error', 'Could not read QR from image. Try a clearer photo.');
            }
        }

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

        function renderPersonSearchResults(people) {
            if (!people.length) {
                personSearchResultsEl.innerHTML = '<li class="text-xs text-slate-400 text-center py-2">No matching member or visitor found.</li>';
                return;
            }

            personSearchResultsEl.innerHTML = '';
            people.forEach((person) => {
                const li = document.createElement('li');
                li.className = 'rounded-xl border border-[var(--color-surface-200)] p-2.5';
                li.innerHTML = `
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-medium text-sm text-[var(--color-ink-950)] truncate">${escHtml(person.name)}</p>
                            <p class="text-xs text-slate-400">${escHtml(person.type)} • ${escHtml(person.meta || 'No details')}</p>
                        </div>
                        <button type="button" class="btn-secondary text-xs px-3 py-1.5" data-record-person data-person-id="${person.id}" data-person-type="${escHtml(person.type)}" data-person-name="${escHtml(person.name)}">
                            Record
                        </button>
                    </div>
                `;
                personSearchResultsEl.appendChild(li);
            });
        }

        async function searchPeopleByName() {
            const q = personSearchInput.value.trim();

            if (q.length < 2) {
                window.showToast('warning', 'Type at least 2 letters to search.');
                return;
            }

            personSearchResultsEl.innerHTML = '<li class="text-xs text-slate-400 text-center py-2">Searching...</li>';

            try {
                const url = new URL(scanSearchPeopleUrl, window.location.origin);
                url.searchParams.set('q', q);

                const resp = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' },
                });

                const json = await resp.json();

                if (!resp.ok) {
                    throw new Error(json.error || 'Search failed.');
                }

                renderPersonSearchResults(Array.isArray(json.people) ? json.people : []);
            } catch (error) {
                personSearchResultsEl.innerHTML = '<li class="text-xs text-red-500 text-center py-2">Search failed. Try again.</li>';
            }
        }

        async function recordSelectedPerson(personType, personId) {
            const serviceId = serviceEl.value;

            if (!serviceId) {
                window.showToast('warning', 'Please select a service before recording attendance.');
                return;
            }

            try {
                const resp = await fetch(scanRecordPersonUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        service_id: serviceId,
                        person_type: personType,
                        person_id: Number(personId),
                    }),
                });

                const json = await resp.json();

                if (!resp.ok || !json.success) {
                    showResult(`<i class="fa-solid fa-circle-xmark text-red-400 text-xl"></i><p class="mt-1 text-sm text-red-600">${escHtml(json.error || 'Unable to record attendance.')}</p>`, 'error');
                    return;
                }

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
                        <p class="text-xs text-slate-400">${escHtml(json.time)}</p>
                    </div>`,
                    'ok'
                );

                prependLog(json.name, json.type, statusLabel, json.time, badgeColor);
                window.showToast('success', `${json.name} marked as ${statusLabel.toLowerCase()}.`);
            } catch {
                showResult('<p class="text-red-500 text-sm">Network error while recording attendance.</p>', 'error');
            }
        }

        // Start camera
        startBtn.addEventListener('click', () => {
            startScanner();
        });

        // Stop camera
        stopBtn.addEventListener('click', async () => {
            try {
                if (html5QrCode) {
                    await html5QrCode.stop();
                    await html5QrCode.clear();
                }
            } catch {
                // ignore stop errors
            } finally {
                html5QrCode = null;
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                lastToken = null;
            }
        });

        // Manual entry
        manualBtn.addEventListener('click', () => {
            const token = manualTokenEl.value.trim();
            if (token) { handleToken(token); manualTokenEl.value = ''; }
        });

        uploadQrBtn.addEventListener('click', () => {
            qrImageInput.click();
        });

        qrImageInput.addEventListener('change', () => {
            const file = qrImageInput.files && qrImageInput.files[0] ? qrImageInput.files[0] : null;
            scanFromImageFile(file);
            qrImageInput.value = '';
        });

        manualTokenEl.addEventListener('keydown', e => {
            if (e.key === 'Enter') manualBtn.click();
        });

        personSearchBtn.addEventListener('click', () => {
            searchPeopleByName();
        });

        personSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchPeopleByName();
            }
        });

        personSearchInput.addEventListener('input', () => {
            const q = personSearchInput.value.trim();

            if (searchDebounceTimer) {
                clearTimeout(searchDebounceTimer);
            }

            if (q.length === 0) {
                personSearchResultsEl.innerHTML = '<li class="text-xs text-slate-400 text-center py-2">Search results will appear here.</li>';
                return;
            }

            if (q.length < 2) {
                personSearchResultsEl.innerHTML = '<li class="text-xs text-slate-400 text-center py-2">Type at least 2 letters to see matches.</li>';
                return;
            }

            searchDebounceTimer = setTimeout(() => {
                searchPeopleByName();
            }, 240);
        });

        personSearchResultsEl.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-record-person]');
            if (!btn) {
                return;
            }

            const personId = btn.getAttribute('data-person-id');
            const personType = btn.getAttribute('data-person-type');
            if (!personId || !personType) {
                return;
            }

            recordSelectedPerson(personType, personId);
        });
    </script>
    @endpush
    </div>
</x-layouts.app>
