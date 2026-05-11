<x-layouts.app title="Prayer Requests">
    <section class="space-y-6">
        <article class="surface-card p-6">
            <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">New prayer request</h3>
            <form method="POST" action="{{ route('prayer-requests.store') }}" class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @csrf
                <select name="member_id" class="form-input"><option value="">Member</option>@foreach($members as $member)<option value="{{ $member->id }}">{{ $member->full_name }}</option>@endforeach</select>
                <select name="visitor_id" class="form-input"><option value="">Visitor</option>@foreach($visitors as $visitor)<option value="{{ $visitor->id }}">{{ $visitor->full_name }}</option>@endforeach</select>
                <input name="request_type" class="form-input" placeholder="Request type" required>
                <select name="visibility" class="form-input" required><option value="private">Private</option><option value="leadership">Leadership</option><option value="public">Public</option></select>
                <select name="status" class="form-input" required><option value="open">Open</option><option value="in_progress">In progress</option><option value="answered">Answered</option><option value="closed">Closed</option></select>
                <select name="assigned_to" class="form-input"><option value="">Assign leader</option>@foreach($leaders as $leader)<option value="{{ $leader->id }}">{{ $leader->full_name }}{{ $leader->role ? ' - '.$leader->role : '' }}</option>@endforeach</select>
                <textarea name="request_text" class="form-input xl:col-span-2" rows="3" placeholder="Prayer details" required></textarea>
                <button class="btn-primary xl:col-span-4" type="submit">Create request</button>
            </form>
        </article>

        <article class="surface-card p-6">
            <form method="GET" action="{{ route('prayer-requests.index') }}" class="mb-4 flex gap-3 max-w-sm">
                <select name="status" class="form-input">
                    <option value="">All statuses</option>
                    @foreach(['open','in_progress','answered','closed'] as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ str_replace('_',' ',ucfirst($option)) }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary" type="submit">Filter</button>
            </form>

            <div class="space-y-3">
                @forelse($requests as $item)
                    <a href="{{ route('prayer-requests.show', $item) }}" class="block rounded-xl border border-[var(--color-surface-200)] p-4 hover:bg-[var(--color-surface-50)]">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-[var(--color-ink-950)]">{{ $item->request_type }}</p>
                            <x-ui.status-badge :status="$item->status" />
                        </div>
                        <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $item->request_text }}</p>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">No prayer requests recorded.</p>
                @endforelse
            </div>

            <div class="mt-6">{{ $requests->links() }}</div>
        </article>
    </section>
</x-layouts.app>
