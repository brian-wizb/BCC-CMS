<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Event;
use App\Models\VolunteerAssignment;
use App\Models\Zone;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function departments(): View
    {
        $departments = $this->departmentReportRows();

        return view('reports.departments', compact('departments'));
    }

    public function departmentsExport(): StreamedResponse
    {
        $departments = $this->departmentReportRows();

        return response()->streamDownload(function () use ($departments): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['department', 'members', 'attendance_records', 'completed_assignments']);
            foreach ($departments as $department) {
                fputcsv($handle, [
                    $department['name'],
                    $department['members'],
                    $department['attendance'],
                    $department['completed_assignments'],
                ]);
            }
            fclose($handle);
        }, 'department-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function zones(): View
    {
        $zones = $this->zoneReportRows();

        return view('reports.zones', compact('zones'));
    }

    public function zonesExport(): StreamedResponse
    {
        $zones = $this->zoneReportRows();

        return response()->streamDownload(function () use ($zones): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['zone', 'members', 'attendance_records']);
            foreach ($zones as $zone) {
                fputcsv($handle, [
                    $zone['name'],
                    $zone['members'],
                    $zone['attendance'],
                ]);
            }
            fclose($handle);
        }, 'zone-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function events(Request $request): View
    {
        ['departmentId' => $departmentId, 'zone' => $zone] = $this->extractFilters($request);

        $events = $this->eventReportQuery($departmentId, $zone)
            ->paginate(15)
            ->withQueryString();

        return view('reports.events', [
            'events' => $events,
            'departmentId' => $departmentId,
            'zone' => $zone,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'zones' => Zone::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function eventsExport(Request $request): StreamedResponse
    {
        ['departmentId' => $departmentId, 'zone' => $zone] = $this->extractFilters($request);

        $events = $this->eventReportQuery($departmentId, $zone)->get();

        return response()->streamDownload(function () use ($events): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['event', 'date', 'status', 'registrations', 'volunteer_assignments']);
            foreach ($events as $event) {
                fputcsv($handle, [
                    $event->title,
                    optional($event->start_date)->toDateString(),
                    $event->status,
                    $event->registrations_count,
                    $event->volunteer_assignments_count,
                ]);
            }
            fclose($handle);
        }, 'event-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function volunteers(Request $request): View
    {
        ['departmentId' => $departmentId, 'zone' => $zone] = $this->extractFilters($request);

        $assignmentsQuery = $this->volunteerReportQuery($departmentId, $zone);

        $assignments = $assignmentsQuery
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $summaryBase = $this->volunteerReportQuery($departmentId, $zone);

        $summary = [
            'total' => (clone $summaryBase)->count(),
            'assigned' => (clone $summaryBase)->where('status', 'assigned')->count(),
            'confirmed' => (clone $summaryBase)->where('status', 'confirmed')->count(),
            'completed' => (clone $summaryBase)->where('status', 'completed')->count(),
            'cancelled' => (clone $summaryBase)->where('status', 'cancelled')->count(),
        ];

        return view('reports.volunteers', [
            'assignments' => $assignments,
            'summary' => $summary,
            'departmentId' => $departmentId,
            'zone' => $zone,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'zones' => Zone::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function volunteersExport(Request $request): StreamedResponse
    {
        ['departmentId' => $departmentId, 'zone' => $zone] = $this->extractFilters($request);

        $assignments = $this->volunteerReportQuery($departmentId, $zone)
            ->with(['member:id,full_name', 'event:id,title', 'department:id,name'])
            ->orderByDesc('id')
            ->get();

        return response()->streamDownload(function () use ($assignments): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['volunteer', 'role', 'event', 'department', 'status']);
            foreach ($assignments as $assignment) {
                fputcsv($handle, [
                    $assignment->member?->full_name,
                    $assignment->role,
                    $assignment->event?->title,
                    $assignment->department?->name,
                    $assignment->status,
                ]);
            }
            fclose($handle);
        }, 'volunteer-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function eventReportQuery(?int $departmentId, string $zone)
    {
        $eventsQuery = Event::query()
            ->withCount(['registrations', 'volunteerAssignments'])
            ->orderByDesc('start_date');

        if ($departmentId) {
            $eventsQuery->whereHas('volunteerAssignments', fn ($query) => $query->where('department_id', $departmentId));
        }

        if ($zone !== '') {
            $eventsQuery->whereHas('registrations.member', fn ($query) => $query->where('zone', $zone));
        }

        return $eventsQuery;
    }

    private function volunteerReportQuery(?int $departmentId, string $zone)
    {
        $query = VolunteerAssignment::query()
            ->with(['member:id,full_name', 'event:id,title,start_date', 'department:id,name'])
            ->orderByDesc('id');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($zone !== '') {
            $query->whereHas('member', fn ($memberQuery) => $memberQuery->where('zone', $zone));
        }

        return $query;
    }

    private function extractFilters(Request $request): array
    {
        return [
            'departmentId' => $request->integer('department_id') ?: null,
            'zone' => trim((string) $request->string('zone')),
        ];
    }

    private function departmentReportRows()
    {
        return Department::query()
            ->withCount('memberships')
            ->get()
            ->map(function (Department $department): array {
                $attendanceCount = AttendanceRecord::query()
                    ->where('department_id', $department->id)
                    ->count();

                $completedAssignments = VolunteerAssignment::query()
                    ->where('department_id', $department->id)
                    ->where('status', 'completed')
                    ->count();

                return [
                    'name' => $department->name,
                    'members' => $department->memberships_count,
                    'attendance' => $attendanceCount,
                    'completed_assignments' => $completedAssignments,
                ];
            })
            ->sortByDesc('attendance')
            ->values();
    }

    private function zoneReportRows()
    {
        return Zone::query()
            ->withCount('memberships')
            ->get()
            ->map(function (Zone $zone): array {
                $attendanceCount = AttendanceRecord::query()
                    ->where('zone', $zone->name)
                    ->count();

                return [
                    'name' => $zone->name,
                    'members' => $zone->memberships_count,
                    'attendance' => $attendanceCount,
                ];
            })
            ->sortByDesc('attendance')
            ->values();
    }
}
