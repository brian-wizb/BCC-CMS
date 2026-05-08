<x-layouts.app title="Open Pastoral Case">
    <section class="surface-card p-6">
        <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Open pastoral case</h3>
        <form method="POST" action="{{ route('pastoral-care.store') }}" class="mt-6 grid gap-3 md:grid-cols-2">
            @csrf
            <select name="member_id" class="form-input"><option value="">Member</option>@foreach($members as $member)<option value="{{ $member->id }}">{{ $member->full_name }}</option>@endforeach</select>
            <select name="family_id" class="form-input"><option value="">Family</option>@foreach($families as $family)<option value="{{ $family->id }}">{{ $family->head_of_family }}</option>@endforeach</select>
            <input name="case_type" class="form-input" placeholder="Case type" required>
            <select name="priority" class="form-input" required><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select>
            <select name="status" class="form-input" required><option value="open">Open</option><option value="in_progress">In progress</option><option value="closed">Closed</option></select>
            <select name="assigned_to" class="form-input"><option value="">Assign user</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->full_name ?: $user->username }}</option>@endforeach</select>
            <textarea name="summary" class="form-input md:col-span-2" rows="4" placeholder="Case summary"></textarea>
            <button type="submit" class="btn-primary md:col-span-2">Create case</button>
        </form>
    </section>
</x-layouts.app>
