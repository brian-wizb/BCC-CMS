<x-layouts.app title="Event Details">
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <article class="surface-card p-6">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">{{ $event->title }}</h3>
                <x-ui.status-badge :status="$event->status" />
            </div>
            <p class="mt-2 text-sm text-slate-600">{{ $event->description ?: 'No description' }}</p>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Participant</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse ($event->registrations as $registration)
                            <tr>
                                <td class="px-4 py-4">{{ $registration->member?->full_name ?: $registration->visitor?->full_name ?: 'Unknown' }}</td>
                                <td class="px-4 py-4">{{ $registration->member_id ? 'Member' : 'Visitor' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <x-ui.status-badge :status="$registration->status" />
                                        <form method="POST" action="{{ route('events.registrations.destroy', [$event, $registration]) }}" data-confirm="Remove this registration?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600">Remove</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No registrations yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('events.destroy', $event) }}" class="mt-5" data-confirm="Delete this event?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-secondary text-red-600">Delete event</button>
            </form>
        </article>

        <aside class="surface-card p-6">
            <h4 class="text-lg font-semibold text-[var(--color-ink-950)]">Update event</h4>
            <form method="POST" action="{{ route('events.update', $event) }}" class="mt-4 space-y-3">
                @csrf
                @method('PUT')
                <input name="title" class="form-input" value="{{ $event->title }}" required>
                <input name="event_type" class="form-input" value="{{ $event->event_type }}" placeholder="Event type">
                <input type="date" name="start_date" class="form-input" value="{{ optional($event->start_date)->toDateString() }}" required>
                <input type="date" name="end_date" class="form-input" value="{{ optional($event->end_date)->toDateString() }}">
                <input type="time" name="start_time" class="form-input" value="{{ $event->start_time ?: '' }}">
                <input type="time" name="end_time" class="form-input" value="{{ $event->end_time ?: '' }}">
                <input name="location" class="form-input" value="{{ $event->location }}" placeholder="Location">
                <select name="status" class="form-input" required>
                    @foreach (['planned', 'ongoing', 'completed', 'cancelled'] as $statusOption)
                        <option value="{{ $statusOption }}" @selected($event->status === $statusOption)>{{ ucfirst($statusOption) }}</option>
                    @endforeach
                </select>
                <textarea name="description" class="form-input" rows="3" placeholder="Description">{{ $event->description }}</textarea>
                <button type="submit" class="btn-secondary w-full">Update event</button>
            </form>

            <h4 class="text-lg font-semibold text-[var(--color-ink-950)]">Register participant</h4>
            <form method="POST" action="{{ route('events.registrations.store', $event) }}" class="mt-4 space-y-3">
                @csrf
                <select name="member_id" class="form-input">
                    <option value="">Member</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                    @endforeach
                </select>
                <select name="visitor_id" class="form-input">
                    <option value="">Visitor</option>
                    @foreach ($visitors as $visitor)
                        <option value="{{ $visitor->id }}">{{ $visitor->full_name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-input">
                    <option value="registered">Registered</option>
                    <option value="attended">Attended</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button type="submit" class="btn-primary w-full">Save registration</button>
            </form>
        </aside>
    </section>
</x-layouts.app>
