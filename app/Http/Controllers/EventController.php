<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Department;
use App\Models\Member;
use App\Models\Visitor;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $departmentId = $request->integer('department_id') ?: null;
        $zone = trim((string) $request->string('zone'));

        $eventsQuery = Event::query()->latest('start_date');

        if ($departmentId) {
            $eventsQuery->whereHas('volunteerAssignments', fn ($query) => $query->where('department_id', $departmentId));
        }

        if ($zone !== '') {
            $eventsQuery->whereHas('registrations.member', fn ($query) => $query->where('zone', $zone));
        }

        return view('events.index', [
            'events' => $eventsQuery->paginate(10)->withQueryString(),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'zones' => Zone::query()->orderBy('name')->get(['id', 'name']),
            'departmentId' => $departmentId,
            'zone' => $zone,
        ]);
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['planned', 'ongoing', 'completed', 'cancelled'])],
        ]);

        $data['created_by'] = auth()->id();
        $event = Event::query()->create($data);

        return redirect()->route('events.show', $event)->with('status', 'Event created successfully.');
    }

    public function show(Event $event): View
    {
        return view('events.show', [
            'event' => $event->load(['registrations.member', 'registrations.visitor', 'volunteerAssignments.member']),
            'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
            'visitors' => Visitor::query()->orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['planned', 'ongoing', 'completed', 'cancelled'])],
        ]);

        $event->update($data);

        return back()->with('status', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events.index')->with('status', 'Event deleted successfully.');
    }

    public function register(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['nullable', 'integer', Rule::exists('members', 'id')],
            'visitor_id' => ['nullable', 'integer', Rule::exists('visitors', 'id')],
            'status' => ['required', 'string', Rule::in(['registered', 'attended', 'cancelled'])],
        ]);

        EventRegistration::query()->create([
            'event_id' => $event->id,
            'member_id' => $data['member_id'] ?? null,
            'visitor_id' => $data['visitor_id'] ?? null,
            'status' => $data['status'],
            'registered_at' => now(),
        ]);

        return back()->with('status', 'Registration recorded successfully.');
    }

    public function destroyRegistration(Event $event, EventRegistration $registration): RedirectResponse
    {
        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        $registration->delete();

        return back()->with('status', 'Registration removed successfully.');
    }
}
