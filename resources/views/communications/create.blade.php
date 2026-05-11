<x-layouts.app title="Compose Communication">
    <section class="surface-card p-6 max-w-3xl">
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Leadership Operations</p>
            <h2 class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">Compose communication</h2>
        </div>

        <form method="POST" action="{{ route('communications.store') }}" class="space-y-5" id="compose-form">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Channel --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Channel</label>
                    <select name="channel" id="channel-select" class="form-input w-full" required>
                        <option value="sms"      @selected(old('channel') === 'sms')>SMS</option>
                        <option value="whatsapp" @selected(old('channel') === 'whatsapp')>WhatsApp</option>
                    </select>
                    @error('channel')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Audience --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Audience</label>
                    <select name="audience_type" class="form-input w-full" required>
                        <option value="all_members" @selected(old('audience_type') === 'all_members')>All Members</option>
                        <option value="all_visitors" @selected(old('audience_type') === 'all_visitors')>All Visitors</option>
                        <option value="everyone" @selected(old('audience_type') === 'everyone')>Everyone (Members + Visitors)</option>
                    </select>
                    @error('audience_type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Subject --}}
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Subject <span class="font-normal normal-case text-slate-400">(optional)</span>
                </label>
                <input name="subject" class="form-input w-full" value="{{ old('subject') }}" placeholder="e.g. Sunday Service Reminder">
                @error('subject')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Message --}}
            <div>
                <div class="mb-1 flex items-end justify-between">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                    <span id="sms-counter" class="text-xs text-slate-400 hidden">
                        <span id="char-count">0</span> / 160 chars
                        <span id="sms-parts" class="ml-1 font-semibold text-amber-600 hidden">(multi-part SMS)</span>
                    </span>
                </div>
                <textarea name="message" id="message-input" class="form-input w-full" rows="6"
                          placeholder="Type your message…" required>{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Save draft</button>
                <a href="{{ route('communications.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>

    @push('scripts')
    <script>
        const channelSelect = document.getElementById('channel-select');
        const messageInput  = document.getElementById('message-input');
        const smsCounter    = document.getElementById('sms-counter');
        const charCount     = document.getElementById('char-count');
        const smsParts      = document.getElementById('sms-parts');

        function updateCounter() {
            const len = messageInput.value.length;
            charCount.textContent = len;
            if (channelSelect.value === 'sms') {
                smsCounter.classList.remove('hidden');
                if (len > 160) {
                    smsParts.classList.remove('hidden');
                    smsParts.textContent = '(' + Math.ceil(len / 153) + '-part SMS)';
                } else {
                    smsParts.classList.add('hidden');
                }
            } else {
                smsCounter.classList.add('hidden');
            }
        }

        channelSelect.addEventListener('change', updateCounter);
        messageInput.addEventListener('input', updateCounter);
        updateCounter(); // run on page load to handle old() values
    </script>
    @endpush
</x-layouts.app>
