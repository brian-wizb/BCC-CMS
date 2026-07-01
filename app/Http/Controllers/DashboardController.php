<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Campaign;
use App\Models\DepartmentIncome;
use App\Models\Donation;
use App\Models\Expenditure;
use App\Models\Income;
use App\Models\Member;
use App\Models\Payroll;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\FollowUpTask;
use App\Models\User;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user()?->loadMissing('roles.permissions');
        $today = Carbon::today();

        $ageBuckets = [
            'Above 45 Years' => 0,
            '35-45 Years' => 0,
            '18-34 Years' => 0,
            'Below 18 Years' => 0,
        ];

        $membersForAge = Member::query()->whereNotNull('date_of_birth')->get(['date_of_birth']);
        foreach ($membersForAge as $member) {
            if (! $member->date_of_birth) {
                continue;
            }

            $age = Carbon::parse($member->date_of_birth)->age;
            if ($age > 45) {
                $ageBuckets['Above 45 Years']++;
            } elseif ($age >= 35) {
                $ageBuckets['35-45 Years']++;
            } elseif ($age >= 18) {
                $ageBuckets['18-34 Years']++;
            } else {
                $ageBuckets['Below 18 Years']++;
            }
        }

        $genderBuckets = ['Male' => 0, 'Female' => 0, 'Other' => 0];
        $membersForGender = Member::query()->get(['gender']);
        foreach ($membersForGender as $member) {
            $gender = strtolower(trim((string) $member->gender));
            if ($gender === 'male') {
                $genderBuckets['Male']++;
            } elseif ($gender === 'female') {
                $genderBuckets['Female']++;
            } elseif ($gender !== '') {
                $genderBuckets['Other']++;
            }
        }

        $runningCampaigns = Campaign::query()
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            })
            ->count();

        $unfinishedPledges = (float) Pledge::query()
            ->withSum('payments', 'amount')
            ->get(['id', 'amount'])
            ->sum(function (Pledge $pledge) {
                $paid = (float) ($pledge->payments_sum_amount ?? 0);
                $due = (float) $pledge->amount - $paid;

                return $due > 0 ? $due : 0;
            });

        $donationsTotal = (float) Donation::query()->sum('amount');
        $incomeTotal = (float) Income::query()->sum('amount');
        $deptIncomeTotal = (float) DepartmentIncome::query()->sum('amount');
        $expendituresTotal = (float) Expenditure::query()->sum('amount');
        $payrollTotal = (float) Payroll::query()->sum('paid_amount');

        $timetable = [
            ['day' => 'Every Day', 'session' => 'Mid-Night Prayer Power', 'time' => '22:45 - 00:45'],
            ['day' => 'Friday', 'session' => 'Family Prayer Service', 'time' => '17:30 - 21:00'],
            ['day' => 'Saturday', 'session' => 'Praise and Worship Rehearsal', 'time' => '15:00 - 17:00'],
            ['day' => 'Sunday', 'session' => 'Sunday Service - First Service', 'time' => '07:00 - 09:00'],
            ['day' => 'Sunday', 'session' => 'Sunday Service - Second Service', 'time' => '10:00 - 13:00'],
            ['day' => 'Sunday', 'session' => 'CMF Session', 'time' => '13:00 - 14:00'],
        ];

        $stats = [
            'active_users' => User::query()->active()->count(),
            'members' => Member::query()->count(),
            'families' => \App\Models\Family::query()->count(),
            'departments' => \App\Models\Department::query()->count(),
            'zones' => Zone::query()->count(),
            'income' => Income::query()->count(),
            'expenditures' => Expenditure::query()->count(),
            'donations' => Donation::query()->count(),
            'campaigns' => Campaign::query()->count(),
            'pledges' => Pledge::query()->count(),
            'missed_pledges' => \App\Models\MissedPledge::query()->count(),
            'pledge_payments' => PledgePayment::query()->count(),
            'payroll' => Payroll::query()->count(),
            'volunteers' => \App\Models\VolunteerAssignment::query()->count(),
            'running_campaigns' => $runningCampaigns,
            'unfinished_pledges_total' => round($unfinishedPledges, 2),
            'donations_total' => round($donationsTotal, 2),
            'income_total' => round($incomeTotal + $deptIncomeTotal, 2),
            'expenditures_total' => round($expendituresTotal + $payrollTotal, 2),
            'net_total' => round(($donationsTotal + $incomeTotal + $deptIncomeTotal) - ($expendituresTotal + $payrollTotal), 2),
        ];

        $chartData = [
            'age' => [
                ['name' => 'Above 45 Years', 'value' => $ageBuckets['Above 45 Years']],
                ['name' => '35-45 Years', 'value' => $ageBuckets['35-45 Years']],
                ['name' => '18-34 Years', 'value' => $ageBuckets['18-34 Years']],
                ['name' => 'Below 18 Years', 'value' => $ageBuckets['Below 18 Years']],
            ],
            'gender' => [
                ['name' => 'Male', 'value' => $genderBuckets['Male']],
                ['name' => 'Female', 'value' => $genderBuckets['Female']],
                ['name' => 'Other', 'value' => $genderBuckets['Other']],
            ],
            'finance' => [
                ['name' => 'Donations', 'value' => round($donationsTotal, 2)],
                ['name' => 'Income', 'value' => round($incomeTotal + $deptIncomeTotal, 2)],
                ['name' => 'Expenses', 'value' => round($expendituresTotal + $payrollTotal, 2)],
            ],
        ];

        $openAlertsCount    = Alert::query()->where('status', 'open')->count();
        $criticalAlertsCount = Alert::query()->where('severity', 'critical')->where('status', '!=', 'resolved')->count();

        if ($user?->hasRole('counsellor')) {
            $leader = $user->leader;
            $taskQuery = FollowUpTask::query()->with('leader')->when($leader, fn ($q) => $q->where('leader_id', $leader->id), fn ($q) => $q->whereRaw('0 = 1'));

            $assignedTaskCount = $taskQuery->count();
            $pendingTaskCount = (clone $taskQuery)->where('status', 'pending')->count();
            $inProgressTaskCount = (clone $taskQuery)->where('status', 'in_progress')->count();
            $completedTaskCount = (clone $taskQuery)->where('status', 'completed')->count();
            $overdueTaskCount = (clone $taskQuery)->whereNotIn('status', ['completed'])->where('due_date', '<', Carbon::today())->count();

            $recentTasks = (clone $taskQuery)
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'in_progress' THEN 1 ELSE 2 END")
                ->orderBy('due_date', 'asc')
                ->limit(8)
                ->get();

            return view('dashboard.counsellor', [
                'user' => $user,
                'primaryRole' => $user->primaryRole(),
                'leader' => $leader,
                'assignedTaskCount' => $assignedTaskCount,
                'pendingTaskCount' => $pendingTaskCount,
                'inProgressTaskCount' => $inProgressTaskCount,
                'completedTaskCount' => $completedTaskCount,
                'overdueTaskCount' => $overdueTaskCount,
                'recentTasks' => $recentTasks,
            ]);
        }

        return view('dashboard.index', [
            'user' => $user,
            'primaryRole' => $user?->primaryRole(),
            'stats' => $stats,
            'chartData' => $chartData,
            'timetable' => $timetable,
            'openAlertsCount' => $openAlertsCount,
            'criticalAlertsCount' => $criticalAlertsCount,
        ]);
    }
}
