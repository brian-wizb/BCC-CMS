<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Member;
use App\Models\PastoralCase;
use App\Models\VolunteerAssignment;
use Illuminate\View\View;

class ExecutiveDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.executive', [
            'kpis' => [
                'members' => Member::query()->count(),
                'attendance' => AttendanceRecord::query()->count(),
                'pastoral_open_cases' => PastoralCase::query()->where('status', '!=', 'closed')->count(),
                'volunteer_assignments' => VolunteerAssignment::query()->count(),
            ],
        ]);
    }
}
