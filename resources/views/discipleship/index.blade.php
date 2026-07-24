<x-layouts.app title="Discipleship">
    @php
        $stageStatus = static function ($participant, int $number) {
            return $participant->stages->firstWhere('stage_number', $number)?->status ?? 'not_started';
        };
        $label = static fn (string $value) => str($value)->replace('_', ' ')->title();
    @endphp

    <section class="surface-card p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(124,58,237,.12); color:rgb(124,58,237);"><i class="fas fa-book-open text-lg"></i></span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Ministry Hub</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Discipleship</h3>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('discipleship.create')
                    <a href="{{ route('discipleship.create') }}" class="btn-primary flex items-center gap-1.5"><i class="fas fa-user-plus text-xs"></i> Enrol participant</a>
                @endcan
                <a href="{{ route('discipleship.certificates') }}" class="btn-secondary flex items-center gap-1.5"><i class="fas fa-award text-xs"></i> Awarded certificates</a>
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach (['total' => ['Enrolled', 'fa-users'], 'in_progress' => ['In progress', 'fa-spinner'], 'completed' => ['Completed Foundation 1–4', 'fa-check-circle'], 'awarded' => ['Certificates awarded', 'fa-award']] as $key => [$title, $icon])
                <article class="rounded-xl border border-[var(--color-surface-200)] bg-[var(--color-surface-50)] p-4">
                    <i class="fas {{ $icon }} text-sm text-violet-500"></i>
                    <p class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">{{ number_format($stats[$key]) }}</p>
                    <p class="text-xs text-slate-500">{{ $title }}</p>
                </article>
            @endforeach
        </div>

        <form method="GET" class="mt-5 grid gap-3 md:grid-cols-4">
            <input name="search" class="form-input md:col-span-2" placeholder="Search member, participant, or phone" value="{{ $search }}">
            <select name="stage" class="form-input"><option value="">Any foundation stage</option>@for ($number = 1; $number <= 4; $number++)<option value="{{ $number }}" @selected($stage === $number)>Foundation {{ $number }}</option>@endfor</select>
            <select name="status" class="form-input"><option value="">Any status</option>@foreach (['not_started', 'started', 'in_progress', 'completed', 'deferred'] as $option)<option value="{{ $option }}" @selected($status === $option)>{{ $label($option) }}</option>@endforeach</select>
            <div class="flex gap-2"><button class="btn-secondary">Filter</button><a href="{{ route('discipleship.index') }}" class="btn-secondary">Clear</a></div>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-slate-500"><tr><th class="px-4 py-3 font-medium">Participant</th><th class="px-4 py-3 font-medium">Type</th>@for ($number = 1; $number <= 4; $number++)<th class="px-4 py-3 font-medium">F{{ $number }}</th>@endfor<th class="px-4 py-3 font-medium">Certificate</th><th class="px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                    @forelse ($participants as $participant)
                        <tr class="hover:bg-[var(--color-surface-50)]">
                            <td class="px-4 py-3"><p class="font-medium text-[var(--color-ink-950)]">{{ $participant->display_name }}</p><p class="text-xs text-slate-400">{{ $participant->member?->phone ?: $participant->external_phone ?: 'No phone' }}</p></td>
                            <td class="px-4 py-3 text-slate-500">{{ $participant->member_id ? 'Registered member' : 'External participant' }}</td>
                            @for ($number = 1; $number <= 4; $number++)<td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $label($stageStatus($participant, $number)) }}</span></td>@endfor
                            <td class="px-4 py-3">@if ($participant->certificate_awarded_at)<span class="text-xs font-medium text-emerald-600"><i class="fas fa-award mr-1"></i>{{ $participant->certificate_number }}</span>@else <span class="text-xs text-slate-400">Not awarded</span>@endif</td>
                            <td class="px-4 py-3"><a href="{{ route('discipleship.show', $participant) }}" class="btn-secondary px-2.5 py-1 text-xs">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-12 text-center text-slate-400"><i class="fas fa-book-reader mb-2 block text-3xl text-slate-300"></i>No discipleship participants found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $participants->links() }}</div>
    </section>
</x-layouts.app>
