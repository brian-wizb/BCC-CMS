<x-layouts.app title="Pastoral Case Details">
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <article class="surface-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">{{ $case->case_type }}</h3>
                <x-ui.status-badge :status="$case->status" />
            </div>
            <p class="mt-3 text-sm text-slate-600">{{ $case->summary ?: 'No summary available.' }}</p>

            <form method="POST" action="{{ route('pastoral-care.update', $case) }}" class="mt-6 grid gap-3 md:grid-cols-2">
                @csrf
                @method('PUT')
                <select name="priority" class="form-input" required>@foreach(['low','medium','high'] as $p)<option value="{{ $p }}" @selected($case->priority === $p)>{{ ucfirst($p) }}</option>@endforeach</select>
                <select name="status" class="form-input" required>@foreach(['open','in_progress','closed'] as $s)<option value="{{ $s }}" @selected($case->status === $s)>{{ str_replace('_',' ',ucfirst($s)) }}</option>@endforeach</select>
                <select name="assigned_to" class="form-input"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)$case->assigned_to === (string)$user->id)>{{ $user->full_name ?: $user->username }}</option>@endforeach</select>
                <input name="summary" class="form-input" value="{{ $case->summary }}">
                <button type="submit" class="btn-secondary md:col-span-2">Update case</button>
            </form>

            <div class="mt-8 space-y-2">
                @forelse($case->notes as $note)
                    <div class="rounded-xl border border-[var(--color-surface-200)] p-3">
                        <div class="flex items-center justify-between gap-3">
                            <x-ui.status-badge :status="$note->visibility" tone="info" />
                            <span class="text-xs text-slate-500">{{ $note->created_at?->format('d M Y H:i') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-700">{{ $note->note }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No notes recorded yet.</p>
                @endforelse
            </div>
        </article>

        <aside class="surface-card p-6 space-y-4">
            <form method="POST" action="{{ route('pastoral-care.notes.store', $case) }}">
                @csrf
                <label class="form-label">New case note</label>
                <textarea name="note" rows="4" class="form-input" required></textarea>
                <select name="visibility" class="form-input mt-3" required>
                    <option value="private">Private</option>
                    <option value="leadership">Leadership</option>
                    <option value="public">Public</option>
                </select>
                <button class="btn-primary mt-3 w-full" type="submit">Add note</button>
            </form>

            <form method="POST" action="{{ route('pastoral-care.destroy', $case) }}" onsubmit="return confirm('Delete this pastoral case?');">
                @csrf
                @method('DELETE')
                <button class="btn-secondary w-full text-red-600" type="submit">Delete case</button>
            </form>
        </aside>
    </section>
</x-layouts.app>
