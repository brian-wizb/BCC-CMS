<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\VolunteerAssignment;
use App\Models\Zone;
use Illuminate\View\View;

class ScorecardController extends Controller
{
    public function index(): View
    {
        return view('scorecards.index');
    }

    public function zones(): View
    {
        $zones = Zone::query()->withCount('memberships')->get()->map(function (Zone $zone) {
            $attendanceCount = AttendanceRecord::query()->where('zone', $zone->name)->count();

            return [
                'name' => $zone->name,
                'member_count' => $zone->memberships_count,
                'attendance_count' => $attendanceCount,
            ];
        })->sortByDesc('attendance_count')->values();

        return view('scorecards.zones', compact('zones'));
    }

    public function departments(): View
    {
        $departments = Department::query()->withCount('memberships')->get()->map(function (Department $department) {
            $completedAssignments = VolunteerAssignment::query()
                ->where('department_id', $department->id)
                ->where('status', 'completed')
                ->count();

            return [
                'name' => $department->name,
                'member_count' => $department->memberships_count,
                'completed_assignments' => $completedAssignments,
            ];
        })->sortByDesc('completed_assignments')->values();

        return view('scorecards.departments', compact('departments'));
    }
}
