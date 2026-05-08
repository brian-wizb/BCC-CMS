<x-layouts.app title="Compose Communication">
    <section class="surface-card p-6 max-w-4xl">
        <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Compose communication</h3>
        <form method="POST" action="{{ route('communications.store') }}" class="mt-6 grid gap-3 md:grid-cols-2">
            @csrf
            <select name="channel" class="form-input" required>
                <option value="sms">SMS</option>
                <option value="email">Email</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="internal">Internal</option>
            </select>
            <select name="audience_type" class="form-input" required>
                <option value="all_members">All members</option>
                <option value="all_visitors">All visitors</option>
                <option value="everyone">Everyone</option>
            </select>
            <input name="subject" class="form-input md:col-span-2" placeholder="Subject (optional)">
            <textarea name="message" class="form-input md:col-span-2" rows="6" placeholder="Message" required></textarea>
            <button type="submit" class="btn-primary md:col-span-2">Save draft</button>
        </form>
    </section>
</x-layouts.app>
