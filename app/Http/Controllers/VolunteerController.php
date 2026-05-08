<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Event;
use App\Models\Member;
use App\Models\VolunteerAssignment;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VolunteerController extends Controller
{
    public function index(Request $request): View
    {
        $departmentId = $request->integer('department_id') ?: null;
        $zone = trim((string) $request->string('zone'));

        $assignmentsQuery = VolunteerAssignment::query()
            ->with(['member', 'event', 'department'])
            ->latest('id');

        if ($departmentId) {
            $assignmentsQuery->where('department_id', $departmentId);
        }

        if ($zone !== '') {
            $assignmentsQuery->whereHas('member', fn ($query) => $query->where('zone', $zone));
        }

        return view('volunteers.index', [
            'assignments' => $assignmentsQuery->paginate(10)->withQueryString(),
            'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
            'events' => Event::query()->orderByDesc('start_date')->get(['id', 'title']),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'zones' => Zone::query()->orderBy('name')->get(['id', 'name']),
            'departmentId' => $departmentId,
            'zone' => $zone,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'integer', Rule::exists('members', 'id')],
            'event_id' => ['nullable', 'integer', Rule::exists('events', 'id')],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')],
            'role' => ['required', 'string', 'max:255'],
            'report_time' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['assigned', 'confirmed', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ]);

        VolunteerAssignment::query()->create($data);

        return back()->with('status', 'Volunteer assignment created successfully.');
    }

    public function update(Request $request, VolunteerAssignment $assignment): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'integer', Rule::exists('members', 'id')],
            'event_id' => ['nullable', 'integer', Rule::exists('events', 'id')],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')],
            'role' => ['required', 'string', 'max:255'],
            'report_time' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['assigned', 'confirmed', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ]);

        $assignment->update($data);

        return back()->with('status', 'Volunteer assignment updated successfully.');
    }

    public function destroy(VolunteerAssignment $assignment): RedirectResponse
    {
        $assignment->delete();

        return back()->with('status', 'Volunteer assignment deleted successfully.');
    }
}
