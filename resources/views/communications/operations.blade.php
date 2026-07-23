<x-layouts.app title="Message Operations">
    <section class="surface-card p-6 max-w-4xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Operations</p>
                <h2 class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">Message Credit Operations</h2>
                <p class="mt-1 text-xs text-slate-500">Manage bought credits and monitor SMS usage in real time.</p>
            </div>
            <a href="{{ route('communications.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to communications
            </a>
        </div>

        @if($creditState['is_low'])
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
                <p class="text-sm font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Low credit balance alert</p>
                <p class="mt-1 text-xs">Remaining credits ({{ number_format($creditState['remaining']) }}) are at or below threshold ({{ number_format($creditState['threshold']) }}).</p>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <article class="surface-card p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Credits Purchased</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($creditState['purchased']) }}</p>
            </article>
            <article class="surface-card p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Credits Used</p>
                <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($creditState['used']) }}</p>
            </article>
            <article class="surface-card p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Credits Remaining</p>
                <p class="mt-1 text-2xl font-bold {{ $creditState['is_low'] ? 'text-amber-600' : 'text-emerald-600' }}">{{ number_format($creditState['remaining']) }}</p>
            </article>
        </div>

        <div class="mt-6">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">SMS Usage Breakdown</p>
            <div class="mt-3 grid gap-4 sm:grid-cols-3">
                <article class="surface-card p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">From Communications</p>
                    <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($smsBreakdown['communications']) }}</p>
                </article>
                <article class="surface-card p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">From Givings</p>
                    <p class="mt-1 text-2xl font-bold text-[var(--color-ink-950)]">{{ number_format($smsBreakdown['givings']) }}</p>
                </article>
                <article class="surface-card p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Combined Total</p>
                    <p class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($smsBreakdown['total']) }}</p>
                </article>
            </div>
        </div>

        <form method="POST" action="{{ route('communications.operations.update') }}" class="mt-6 space-y-5">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Credits purchased total</label>
                    <input
                        type="number"
                        min="0"
                        step="1"
                        name="credits_purchased_total"
                        class="form-input w-full"
                        value="{{ old('credits_purchased_total', $creditState['purchased']) }}"
                        required
                    >
                    @error('credits_purchased_total')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-slate-400">Enter the cumulative total credits bought.</p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Low balance threshold</label>
                    <input
                        type="number"
                        min="0"
                        step="1"
                        name="low_balance_threshold"
                        class="form-input w-full"
                        value="{{ old('low_balance_threshold', $creditState['threshold']) }}"
                        required
                    >
                    @error('low_balance_threshold')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-slate-400">Alert appears when remaining credits are less than or equal to this value.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-[var(--color-surface-200)] pt-3">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save credit settings
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>
