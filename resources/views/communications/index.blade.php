<x-layouts.app title="Communications">
    <section class="surface-card p-6">
        <div class="mb-6 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Leadership Operations</p>
                <h2 class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">Communications</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Total SMS messages sent (communications + tithe givings): <span class="font-semibold text-[var(--color-ink-950)]">{{ number_format($smsSentCount) }}</span>
                </p>
            </div>
            <a href="{{ route('communications.create') }}" class="btn-primary">
                <i class="fa-solid fa-pen mr-2"></i> Compose
            </a>
        </div>

        <div class="space-y-3">
            @forelse ($communications as $communication)
                <a href="{{ route('communications.show', $communication) }}"
                   class="flex items-center justify-between gap-4 rounded-2xl border border-[var(--color-surface-200)] p-4 hover:bg-[var(--color-surface-50)] transition-colors">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($communication->channel === 'whatsapp')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                    <i class="fa-solid fa-message"></i> SMS
                                </span>
                            @endif
                            <span class="text-xs text-slate-400">→ {{ str_replace('_', ' ', $communication->audience_type) }}</span>
                        </div>
                        <p class="mt-1 font-semibold text-[var(--color-ink-950)] truncate">{{ $communication->subject ?: 'No subject' }}</p>
                        <p class="mt-0.5 text-xs text-slate-400 truncate">{{ Str::limit($communication->message, 80) }}</p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-1.5">
                        <x-ui.status-badge :status="$communication->status" />
                        @if ($communication->sent_at)
                            <span class="text-xs text-slate-400">{{ $communication->sent_at->format('d M Y') }}</span>
                        @else
                            <span class="text-xs text-slate-300 italic">Draft</span>
                        @endif
                        @if ($communication->deliveries_count > 0)
                            <span class="text-xs text-slate-400">
                                <i class="fa-solid fa-users mr-0.5 text-[10px]"></i>{{ $communication->deliveries_count }}
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 py-16 text-center">
                    <i class="fa-solid fa-comments text-3xl text-slate-300"></i>
                    <p class="mt-3 text-sm text-slate-400">No communications yet. Compose one to get started.</p>
                    <a href="{{ route('communications.create') }}" class="btn-primary mt-4 inline-flex">Compose first message</a>
                </div>
            @endforelse
        </div>

        @if ($communications->hasPages())
            <div class="mt-6">{{ $communications->links() }}</div>
        @endif
    </section>
</x-layouts.app>
