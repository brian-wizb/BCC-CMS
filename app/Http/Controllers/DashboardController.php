<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Campaign;
use App\Models\DepartmentIncome;
use App\Models\Donation;
use App\Models\Expenditure;
use App\Models\Income;
use App\Models\Member;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\FollowUpTask;
use App\Models\Department;
use App\Models\DiscipleshipParticipant;
use App\Models\DiscipleshipStageProgress;
use App\Models\Group;
use App\Models\Leader;
use App\Models\Visitor;
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

        $timetable = [
            ['day' => 'Every Day', 'session' => 'Mid-Night Prayer Power', 'time' => '22:45 - 00:45'],
            ['day' => 'Friday', 'session' => 'Friday Prayer Service', 'time' => '17:30 - 21:00'],
            ['day' => 'Saturday', 'session' => 'Praise and Worship Rehearsal', 'time' => '16:00 - 18:00'],
            ['day' => 'Sunday', 'session' => 'Sunday Service - First Service', 'time' => '06:30 - 08:00'],
            ['day' => 'Sunday', 'session' => 'Sunday Service - Second Service', 'time' => '08:30 - 11:00'],
            ['day' => 'Sunday', 'session' => 'Sunday Service - Third Service', 'time' => '11:30 - 13:30'],
        ];

        $stats = [
            'active_users' => User::query()->active()->count(),
            'members' => Member::query()->count(),
            'departments' => \App\Models\Department::query()->count(),
            'zones' => Zone::query()->count(),
            'income' => Income::query()->count(),
            'expenditures' => Expenditure::query()->count(),
            'donations' => Donation::query()->count(),
            'campaigns' => Campaign::query()->count(),
            'pledges' => Pledge::query()->count(),
            'missed_pledges' => \App\Models\MissedPledge::query()->count(),
            'pledge_payments' => PledgePayment::query()->count(),
            'running_campaigns' => $runningCampaigns,
            'unfinished_pledges_total' => round($unfinishedPledges, 2),
            'donations_total' => round($donationsTotal, 2),
            'income_total' => round($incomeTotal + $deptIncomeTotal, 2),
            'expenditures_total' => round($expendituresTotal, 2),
            'net_total' => round(($donationsTotal + $incomeTotal + $deptIncomeTotal) - $expendituresTotal, 2),
            'discipleship_enrolled' => DiscipleshipParticipant::query()->count(),
            'discipleship_completed' => DiscipleshipParticipant::query()
                ->whereHas('stages', fn ($query) => $query->where('status', 'completed'), '=', 4)
                ->count(),
            'discipleship_awarded' => DiscipleshipParticipant::query()->whereNotNull('certificate_awarded_at')->count(),
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
                ['name' => 'Givings', 'value' => round($donationsTotal, 2)],
                ['name' => 'Income', 'value' => round($incomeTotal + $deptIncomeTotal, 2)],
                ['name' => 'Expenses', 'value' => round($expendituresTotal, 2)],
            ],
        ];

        $openAlertsCount    = Alert::query()->where('status', 'open')->count();
        $criticalAlertsCount = Alert::query()->where('severity', 'critical')->where('status', '!=', 'resolved')->count();

        if ($user?->hasRole('investment_officer')) {
            $monthStart = $today->copy()->startOfMonth();
            $monthEnd = $today->copy()->endOfMonth();

            $incomeStats = [
                'total' => (float) Income::query()->sum('amount'),
                'this_month' => (float) Income::query()
                    ->whereBetween('received_date', [$monthStart, $monthEnd])
                    ->sum('amount'),
                'tithes_total' => (float) Donation::query()
                    ->where('type', 'like', '%Tithe%')
                    ->sum('amount'),
                'income_records' => Income::query()->count(),
                'income_types' => \App\Models\IncomeType::query()->count(),
                'members' => Member::query()->count(),
            ];

            $recentIncome = Income::query()
                ->with(['incomeType:id,type', 'member:id,full_name'])
                ->latest('received_date')
                ->latest('id')
                ->limit(8)
                ->get();

            $recentTithes = Donation::query()
                ->with('member:id,full_name')
                ->where('type', 'like', '%Tithe%')
                ->latest('donation_date')
                ->latest('id')
                ->limit(8)
                ->get();

            $incomeByType = Income::query()
                ->join('income_types', 'incomes.income_type_id', '=', 'income_types.id')
                ->selectRaw('income_types.type as name, SUM(incomes.amount) as value')
                ->groupBy('income_types.id', 'income_types.type')
                ->orderByDesc('value')
                ->limit(8)
                ->get()
                ->map(fn ($item) => ['name' => $item->name, 'value' => (float) $item->value])
                ->values();

            return view('dashboard.investment-officer', [
                'user' => $user,
                'primaryRole' => $user->primaryRole(),
                'incomeStats' => $incomeStats,
                'recentIncome' => $recentIncome,
                'recentTithes' => $recentTithes,
                'incomeByType' => $incomeByType,
            ]);
        }

        if ($user?->hasRole('chief_usher')) {
            $peopleStats = [
                'members' => Member::query()->count(),
                'visitors' => Visitor::query()->count(),
                'children_ministry' => \App\Models\ChildrenMinistry::query()->count(),
                'leaders' => Leader::query()->count(),
                'departments' => Department::query()->count(),
                'zones' => Zone::query()->count(),
                'groups' => Group::query()->count(),
                'follow_up_pending' => FollowUpTask::query()->whereIn('status', ['pending', 'in_progress'])->count(),
                'discipleship_enrolled' => DiscipleshipParticipant::query()->count(),
                'discipleship_in_progress' => DiscipleshipStageProgress::query()
                    ->where('status', 'in_progress')
                    ->distinct('discipleship_participant_id')
                    ->count('discipleship_participant_id'),
                'discipleship_awarded' => DiscipleshipParticipant::query()->whereNotNull('certificate_awarded_at')->count(),
            ];

            $recentMembers = Member::query()
                ->select(['id', 'full_name', 'phone', 'created_at'])
                ->latest('id')
                ->limit(8)
                ->get();

            $recentVisitors = Visitor::query()
                ->select(['id', 'full_name', 'phone', 'first_visit_date'])
                ->latest('id')
                ->limit(8)
                ->get();

            return view('dashboard.chief-usher', [
                'user' => $user,
                'primaryRole' => $user->primaryRole(),
                'peopleStats' => $peopleStats,
                'chartData' => [
                    'age' => $chartData['age'],
                    'gender' => $chartData['gender'],
                ],
                'recentMembers' => $recentMembers,
                'recentVisitors' => $recentVisitors,
            ]);
        }

        if ($user?->hasRole('counsellor')) {
            $leader = $user->leader;
            $taskQuery = FollowUpTask::query()
                ->with(['leader', 'recipients'])
                ->when($leader, fn ($q) => $q->where('leader_id', $leader->id), fn ($q) => $q->whereRaw('0 = 1'));

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
