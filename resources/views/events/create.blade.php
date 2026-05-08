<x-layouts.app title="Add Event">
    <section class="surface-card p-6 max-w-4xl">
        <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Create event</h3>
        <form method="POST" action="{{ route('events.store') }}" class="mt-6 grid gap-3 md:grid-cols-2">
            @csrf
            <input name="title" class="form-input" placeholder="Event title" required>
            <input name="event_type" class="form-input" placeholder="Event type">
            <input type="date" name="start_date" class="form-input" required>
            <input type="date" name="end_date" class="form-input">
            <input type="time" name="start_time" class="form-input">
            <input type="time" name="end_time" class="form-input">
            <input name="location" class="form-input md:col-span-2" placeholder="Location">
            <select name="status" class="form-input" required>
                <option value="planned">Planned</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <textarea name="description" class="form-input md:col-span-2" rows="4" placeholder="Description"></textarea>
            <button type="submit" class="btn-primary md:col-span-2">Create event</button>
        </form>
    </section>
</x-layouts.app>
