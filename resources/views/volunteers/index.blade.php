<x-layouts.app title="Volunteers">
    <section class="space-y-6">
        <article class="surface-card p-6">
            <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Assign volunteer</h3>
            <form method="POST" action="{{ route('volunteers.assignments.store') }}" class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @csrf
                <select name="member_id" class="form-input" required>
                    <option value="">Member</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                    @endforeach
                </select>
                <select name="event_id" class="form-input">
                    <option value="">Event</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                    @endforeach
                </select>
                <select name="department_id" class="form-input">
                    <option value="">Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                <input name="role" class="form-input" placeholder="Role" required>
                <input type="datetime-local" name="report_time" class="form-input">
                <select name="status" class="form-input" required>
                    <option value="assigned">Assigned</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input name="notes" class="form-input xl:col-span-3" placeholder="Notes">
                <button type="submit" class="btn-primary xl:col-span-3">Create assignment</button>
            </form>
        </article>

        <article class="surface-card p-6">
            <x-ui.department-zone-filters
                :action="route('volunteers.index')"
                :departments="$departments"
                :zones="$zones"
                :department-id="$departmentId"
                :zone="$zone"
            />

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Volunteer</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Event</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td class="px-4 py-4">{{ $assignment->member?->full_name ?: '—' }}</td>
                                <td class="px-4 py-4">{{ $assignment->role }}</td>
                                <td class="px-4 py-4">{{ $assignment->event?->title ?: '—' }}</td>
                                <td class="px-4 py-4">{{ $assignment->department?->name ?: '—' }}</td>
                                <td class="px-4 py-4"><x-ui.status-badge :status="$assignment->status" /></td>
                                <td class="px-4 py-4">
                                    <form method="POST" action="{{ route('volunteers.assignments.update', $assignment) }}" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="member_id" value="{{ $assignment->member_id }}">
                                        <input type="hidden" name="event_id" value="{{ $assignment->event_id }}">
                                        <input type="hidden" name="department_id" value="{{ $assignment->department_id }}">
                                        <input type="hidden" name="role" value="{{ $assignment->role }}">
                                        <input type="hidden" name="report_time" value="{{ optional($assignment->report_time)->format('Y-m-d H:i:s') }}">
                                        <input type="hidden" name="notes" value="{{ $assignment->notes }}">
                                        <select name="status" class="form-input" onchange="this.form.submit()">
                                            @foreach (['assigned', 'confirmed', 'completed', 'cancelled'] as $statusOption)
                                                <option value="{{ $statusOption }}" @selected($assignment->status === $statusOption)>{{ ucfirst($statusOption) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <form method="POST" action="{{ route('volunteers.assignments.destroy', $assignment) }}" class="mt-2" data-confirm="Delete this assignment?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No volunteer assignments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $assignments->links() }}</div>
        </article>
    </section>
</x-layouts.app>
