<x-layouts.app title="Open Care Request">
    <section class="surface-card p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Open care request</h3>
            <a href="{{ route('pastoral-care.index') }}" class="btn-secondary">Back to list</a>
        </div>
        <form method="POST" action="{{ route('pastoral-care.store') }}" class="mt-6 grid gap-3 md:grid-cols-2">
            @csrf
            <select name="member_id" class="form-input"><option value="">Member</option>@foreach($members as $member)<option value="{{ $member->id }}">{{ $member->full_name }}</option>@endforeach</select>
            <select name="family_id" class="form-input"><option value="">Family</option>@foreach($families as $family)<option value="{{ $family->id }}">{{ $family->head_of_family }}</option>@endforeach</select>
            <select name="case_type" class="form-input" required>
                <option value="prayer_support">Prayer support</option>
                <option value="counseling">Counseling</option>
                <option value="hospital_visit">Hospital visit</option>
                <option value="bereavement">Bereavement</option>
                <option value="discipleship">Discipleship</option>
                <option value="family_support">Family support</option>
            </select>
            <select name="priority" class="form-input" required><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select>
            <select name="status" class="form-input" required><option value="open">Open</option><option value="in_progress">In progress</option><option value="answered">Answered</option><option value="closed">Closed</option></select>
            <select name="assigned_to" class="form-input"><option value="">Assign leader</option>@foreach($leaders as $leader)<option value="{{ $leader->id }}">{{ $leader->full_name }}{{ $leader->role ? ' - '.$leader->role : '' }}</option>@endforeach</select>
            <textarea name="summary" class="form-input md:col-span-2" rows="4" placeholder="Request summary"></textarea>
            <button type="submit" class="btn-primary md:col-span-2">Create request</button>
        </form>
    </section>
</x-layouts.app>
