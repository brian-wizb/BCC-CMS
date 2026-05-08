<x-layouts.app title="Communication Details">
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <article class="surface-card p-6">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">{{ strtoupper($communication->channel) }} message</h3>
                <x-ui.status-badge :status="$communication->status" />
            </div>
            <p class="mt-2 text-sm text-slate-600">Audience: {{ str_replace('_', ' ', $communication->audience_type) }}</p>
            <h4 class="mt-5 font-semibold text-[var(--color-ink-950)]">{{ $communication->subject ?: 'No subject' }}</h4>
            <p class="mt-2 whitespace-pre-wrap text-sm text-slate-700">{{ $communication->message }}</p>

            <div class="mt-8 overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Recipient</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse ($communication->deliveries as $delivery)
                            <tr>
                                <td class="px-4 py-4">{{ $delivery->recipient_type }} #{{ $delivery->recipient_id }}</td>
                                <td class="px-4 py-4">{{ $delivery->recipient_contact ?: '—' }}</td>
                                <td class="px-4 py-4"><x-ui.status-badge :status="$delivery->delivery_status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No deliveries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="surface-card p-6">
            @if ($communication->status !== 'sent')
                <form method="POST" action="{{ route('communications.update', $communication) }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <select name="channel" class="form-input" required>
                        @foreach (['sms', 'email', 'whatsapp', 'internal'] as $channel)
                            <option value="{{ $channel }}" @selected($communication->channel === $channel)>{{ strtoupper($channel) }}</option>
                        @endforeach
                    </select>
                    <select name="audience_type" class="form-input" required>
                        @foreach (['all_members', 'all_visitors', 'everyone'] as $audience)
                            <option value="{{ $audience }}" @selected($communication->audience_type === $audience)>{{ str_replace('_', ' ', ucfirst($audience)) }}</option>
                        @endforeach
                    </select>
                    <input name="subject" class="form-input" value="{{ $communication->subject }}" placeholder="Subject (optional)">
                    <textarea name="message" class="form-input" rows="4" required>{{ $communication->message }}</textarea>
                    <button type="submit" class="btn-secondary w-full">Update draft</button>
                </form>
            @endif

            <form method="POST" action="{{ route('communications.send', $communication) }}">
                @csrf
                <button type="submit" class="btn-primary w-full">Send communication</button>
            </form>

            <form method="POST" action="{{ route('communications.destroy', $communication) }}" class="mt-3" onsubmit="return confirm('Delete this communication?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-secondary w-full text-red-600">Delete communication</button>
            </form>
        </aside>
    </section>
</x-layouts.app>
