<x-layouts.app title="Care Requests">
    <section class="surface-card p-6">
        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Care Requests</p>
                <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">All care requests</h3>
            </div>
            <a href="{{ route('pastoral-care.create') }}" class="btn-primary">Open request</a>
        </div>

        <form method="GET" action="{{ route('pastoral-care.index') }}" class="mt-5 grid gap-3 md:grid-cols-4">
            <select name="case_type" class="form-input">
                <option value="">All types</option>
                <option value="prayer_support" @selected(($caseType ?? '') === 'prayer_support')>Prayer support</option>
                <option value="counseling" @selected(($caseType ?? '') === 'counseling')>Counseling</option>
                <option value="hospital_visit" @selected(($caseType ?? '') === 'hospital_visit')>Hospital visit</option>
                <option value="bereavement" @selected(($caseType ?? '') === 'bereavement')>Bereavement</option>
                <option value="discipleship" @selected(($caseType ?? '') === 'discipleship')>Discipleship</option>
                <option value="family_support" @selected(($caseType ?? '') === 'family_support')>Family support</option>
            </select>
            <select name="status" class="form-input">
                <option value="">All statuses</option>
                @foreach (['open', 'in_progress', 'answered', 'closed'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ str_replace('_', ' ', ucfirst($option)) }}</option>
                @endforeach
            </select>
            <select name="priority" class="form-input">
                <option value="">All priorities</option>
                @foreach (['low', 'medium', 'high'] as $option)
                    <option value="{{ $option }}" @selected($priority === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>

        <div class="mt-6 space-y-3">
            @forelse ($cases as $case)
                <a href="{{ route('pastoral-care.show', $case) }}" class="block rounded-2xl border border-[var(--color-surface-200)] p-4 hover:bg-[var(--color-surface-50)]">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-[var(--color-ink-950)]">{{ str_replace('_', ' ', ucfirst($case->case_type)) }}</p>
                        <x-ui.status-badge :status="$case->status" />
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $case->summary ?: 'No summary' }}</p>
                </a>
            @empty
                <p class="text-sm text-slate-400">No care requests yet.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $cases->links() }}</div>
    </section>
</x-layouts.app>
