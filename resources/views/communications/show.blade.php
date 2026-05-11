<x-layouts.app title="Communication Details">
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">

        {{-- â”€â”€ Main panel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="space-y-6">

            {{-- Header --}}
            <article class="surface-card p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                            {{ strtoupper($communication->channel) }} Â· {{ str_replace('_', ' ', $communication->audience_type) }}
                        </p>
                        <h2 class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">
                            {{ $communication->subject ?: 'No subject' }}
                        </h2>
                    </div>
                    <x-ui.status-badge :status="$communication->status" />
                </div>

                <p class="mt-4 whitespace-pre-wrap rounded-xl bg-slate-50 p-4 text-sm text-slate-700 leading-relaxed">{{ $communication->message }}</p>

                {{-- Meta row --}}
                <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-400">
                    @if ($communication->creator)
                        <span><i class="fa-solid fa-user mr-1"></i>Created by {{ $communication->creator->name }}</span>
                    @endif
                    @if ($communication->sent_at)
                        <span><i class="fa-solid fa-paper-plane mr-1"></i>Sent {{ $communication->sent_at->format('d M Y, H:i') }}</span>
                    @else
                        <span class="italic">Not yet sent</span>
                    @endif
                </div>
            </article>

            {{-- Delivery stats --}}
            @if ($communication->deliveries()->exists())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @php
                        $statConfig = [
                            'queued'    => ['bg' => 'bg-slate-100',   'text' => 'text-slate-700',  'icon' => 'fa-clock',        'label' => 'Queued'],
                            'delivered' => ['bg' => 'bg-emerald-50',  'text' => 'text-emerald-700','icon' => 'fa-circle-check', 'label' => 'Delivered'],
                            'failed'    => ['bg' => 'bg-rose-50',     'text' => 'text-rose-700',   'icon' => 'fa-circle-xmark', 'label' => 'Failed'],
                        ];
                    @endphp
                    @foreach ($statConfig as $key => $cfg)
                        <article class="surface-card flex items-center gap-3 p-4">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                <i class="fa-solid {{ $cfg['icon'] }}"></i>
                            </span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $cfg['label'] }}</p>
                                <p class="text-xl font-bold text-[var(--color-ink-950)]">{{ $stats[$key] ?? 0 }}</p>
                            </div>
                        </article>
                    @endforeach
                    <article class="surface-card flex items-center gap-3 p-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total</p>
                            <p class="text-xl font-bold text-[var(--color-ink-950)]">{{ $stats->sum() }}</p>
                        </div>
                    </article>
                </div>
            @endif

            {{-- Deliveries table --}}
            <article class="surface-card overflow-hidden">
                <div class="border-b border-[var(--color-surface-200)] px-5 py-4">
                    <h3 class="font-semibold text-[var(--color-ink-950)]">Delivery log</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                        <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-5 py-3">Recipient</th>
                                <th class="px-5 py-3">Contact</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Provider ref / Error</th>
                                <th class="px-5 py-3">Delivered at</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                            @forelse ($deliveries as $delivery)
                                @php
                                    $recipientName = match ($delivery->recipient_type) {
                                        'member'  => $memberNames[$delivery->recipient_id]  ?? "Member #{$delivery->recipient_id}",
                                        'visitor' => $visitorNames[$delivery->recipient_id] ?? "Visitor #{$delivery->recipient_id}",
                                        default   => ucfirst($delivery->recipient_type) . " #{$delivery->recipient_id}",
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 font-medium text-[var(--color-ink-950)]">{{ $recipientName }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $delivery->recipient_contact ?: 'â€”' }}</td>
                                    <td class="px-5 py-3"><x-ui.status-badge :status="$delivery->delivery_status" /></td>
                                    <td class="px-5 py-3 max-w-xs truncate text-slate-400 text-xs" title="{{ $delivery->provider_response }}">{{ $delivery->provider_response ?: 'â€”' }}</td>
                                    <td class="px-5 py-3 text-slate-400 text-xs whitespace-nowrap">
                                        {{ $delivery->delivered_at?->format('d M Y H:i') ?? 'â€”' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-400">No deliveries recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($deliveries->hasPages())
                    <div class="border-t border-[var(--color-surface-200)] px-5 py-3">
                        {{ $deliveries->links() }}
                    </div>
                @endif
            </article>
        </div>

        {{-- â”€â”€ Sidebar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <aside class="space-y-4">

            @if ($communication->status !== 'sent')
                {{-- Edit draft --}}
                <article class="surface-card p-5">
                    <h3 class="mb-3 font-semibold text-[var(--color-ink-950)]">Edit draft</h3>
                    <form method="POST" action="{{ route('communications.update', $communication) }}" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Channel</label>
                            <select name="channel" class="form-input w-full" required>
                                <option value="sms"      @selected($communication->channel === 'sms')>SMS</option>
                                <option value="whatsapp" @selected($communication->channel === 'whatsapp')>WhatsApp</option>
                            </select>
                            @error('channel')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Audience</label>
                            <select name="audience_type" class="form-input w-full" required>
                                @foreach (['all_members' => 'All Members', 'all_visitors' => 'All Visitors', 'everyone' => 'Everyone'] as $val => $label)
                                    <option value="{{ $val }}" @selected($communication->audience_type === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('audience_type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Subject <span class="font-normal normal-case text-slate-400">(optional)</span></label>
                            <input name="subject" class="form-input w-full" value="{{ old('subject', $communication->subject) }}" placeholder="e.g. Sunday Service Reminder">
                            @error('subject')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                            <textarea name="message" class="form-input w-full" rows="5" required>{{ old('message', $communication->message) }}</textarea>
                            @error('message')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="btn-secondary w-full">Save changes</button>
                    </form>
                </article>

                {{-- Send --}}
                <article class="surface-card p-5">
                    <p class="mb-3 text-sm text-slate-500">Ready to send? This will queue a message for every recipient with a phone number on file.</p>
                    <form method="POST" action="{{ route('communications.send', $communication) }}"
                          onsubmit="return confirm('Send this communication to all recipients? This cannot be undone.');">
                        @csrf
                        <button type="submit" class="btn-primary w-full">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Send now
                        </button>
                    </form>
                </article>
            @else
                <article class="surface-card p-5">
                    <div class="flex items-center gap-2 text-emerald-700">
                        <i class="fa-solid fa-circle-check"></i>
                        <span class="font-semibold">Sent</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-400">{{ $communication->sent_at?->format('d M Y, H:i') }}</p>
                    @if (($stats['failed'] ?? 0) > 0)
                        <p class="mt-2 text-sm text-rose-600">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            {{ $stats['failed'] }} delivery/deliveries failed.
                        </p>
                        <form method="POST" action="{{ route('communications.retry', $communication) }}" class="mt-3"
                              onsubmit="return confirm('Re-queue all failed deliveries?');">
                            @csrf
                            <button type="submit" class="btn-secondary w-full">
                                <i class="fa-solid fa-rotate-right mr-2"></i> Retry failed
                            </button>
                        </form>
                    @else
                        <p class="mt-2 text-sm text-slate-500">All messages dispatched successfully.</p>
                    @endif
                </article>
            @endif

            {{-- Delete --}}
            <form method="POST" action="{{ route('communications.destroy', $communication) }}"
                  onsubmit="return confirm('Permanently delete this communication and all delivery records?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-secondary w-full text-rose-600">
                    <i class="fa-solid fa-trash mr-2"></i> Delete
                </button>
            </form>
        </aside>
    </section>
</x-layouts.app>
