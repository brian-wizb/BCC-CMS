<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\Department;
use App\Models\DiscipleshipParticipant;
use App\Models\DepartmentExpense;
use App\Models\DepartmentIncome;
use App\Models\Donation;
use App\Models\Expenditure;
use App\Models\FollowUpTask;
use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\University;
use App\Models\Visitor;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function departments(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);
        $departments = $this->departmentReportRows($dateFrom, $dateTo);

        return view('reports.departments', compact('departments', 'dateFrom', 'dateTo'));
    }

    public function departmentsExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);
        $departments = $this->departmentReportRows($dateFrom, $dateTo);

        return response()->streamDownload(function () use ($departments): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['department', 'members', 'attendance_records', 'attendance_rate']);
            foreach ($departments as $row) {
                fputcsv($handle, [$row['name'], $row['members'], $row['attendance'], $row['attendance_rate']]);
            }
            fclose($handle);
        }, 'department-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function zones(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);
        $zones = $this->zoneReportRows($dateFrom, $dateTo);

        return view('reports.zones', compact('zones', 'dateFrom', 'dateTo'));
    }

    public function zonesExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);
        $zones = $this->zoneReportRows($dateFrom, $dateTo);

        return response()->streamDownload(function () use ($zones): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['zone', 'leader', 'members', 'attendance_records', 'attendance_rate']);
            foreach ($zones as $row) {
                fputcsv($handle, [$row['name'], $row['leader'], $row['members'], $row['attendance'], $row['attendance_rate']]);
            }
            fclose($handle);
        }, 'zone-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function groups(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);
        $groups = $this->groupReportRows($dateFrom, $dateTo);

        return view('reports.groups', compact('groups', 'dateFrom', 'dateTo'));
    }

    public function groupsExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);
        $groups = $this->groupReportRows($dateFrom, $dateTo);

        return response()->streamDownload(function () use ($groups): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['group', 'registered_members', 'guest_members', 'leaders', 'coordinators', 'members', 'total']);
            foreach ($groups as $row) {
                fputcsv($handle, [
                    $row['name'],
                    $row['registered'],
                    $row['guests'],
                    $row['leaders'],
                    $row['coordinators'],
                    $row['members'],
                    $row['total'],
                ]);
            }
            fclose($handle);
        }, 'groups-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function members(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $maritalStatus = trim((string) $request->string('marital_status'));
        $employmentStatus = trim((string) $request->string('employment_status'));
        $universityStudent = trim((string) $request->string('university_student'));
        $universityId = $request->integer('university_id') ?: null;
        $studyDateFrom = $request->filled('study_date_from') ? $request->string('study_date_from')->toString() : null;
        $studyDateTo = $request->filled('study_date_to') ? $request->string('study_date_to')->toString() : null;

        $base = Member::query()
            ->when($dateFrom, fn ($q) => $q->where('membership_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('membership_date', '<=', $dateTo))
            ->when($maritalStatus !== '', fn ($q) => $q->where('marital_status', $maritalStatus))
            ->when($employmentStatus !== '', fn ($q) => $q->where('employment_status', $employmentStatus))
            ->when($universityStudent !== '', function ($q) use ($universityStudent) {
                if ($universityStudent === 'yes') {
                    $q->where('is_university_student', true);
                } elseif ($universityStudent === 'no') {
                    $q->where('is_university_student', false);
                }
            })
            ->when($universityId, fn ($q) => $q->where('university_id', $universityId))
            ->when($studyDateFrom, fn ($q) => $q->where('university_start_date', '>=', $studyDateFrom))
            ->when($studyDateTo, fn ($q) => $q->where('university_end_date', '<=', $studyDateTo));

        $total = (clone $base)->count();
        $marriedCount = (clone $base)->where('marital_status', 'Married')->count();
        $singleCount = (clone $base)->where('marital_status', 'Single')->count();
        $bornAgain = (clone $base)->where('is_born_again', true)->count();
        $baptized = (clone $base)->where('is_baptized', true)->count();
        $holySpirit = (clone $base)->where('holy_spirit_baptised', true)->count();

        $byGender = (clone $base)->select('gender', DB::raw('count(*) as total'))->groupBy('gender')->pluck('total', 'gender');
        $byMarital = (clone $base)->select('marital_status', DB::raw('count(*) as total'))->groupBy('marital_status')->pluck('total', 'marital_status');
        $byZone = (clone $base)->select('zone', DB::raw('count(*) as total'))->groupBy('zone')->orderByDesc('total')->get();
        $byEmployment = (clone $base)->select('employment_status', DB::raw('count(*) as total'))->groupBy('employment_status')->pluck('total', 'employment_status');
        $universityStudentsCount = (clone $base)->where('is_university_student', true)->count();

        $universityRows = (clone $base)
            ->with('university:id,name')
            ->where('is_university_student', true)
            ->orderBy('full_name')
            ->get([
                'id',
                'full_name',
                'university_id',
                'university_start_date',
                'university_end_date',
                'employment_status',
            ]);

        $universities = University::query()
            ->orderBy('type')
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        $monthlyQ = Member::query()
            ->select(DB::raw("DATE_FORMAT(membership_date,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNotNull('membership_date')
            ->when($dateFrom, fn ($q) => $q->where('membership_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('membership_date', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('membership_date', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month');
        $monthlyGrowth = $monthlyQ->get();

        return view('reports.members', compact(
            'total', 'marriedCount', 'singleCount', 'bornAgain', 'baptized', 'holySpirit',
            'byGender', 'byMarital', 'byZone', 'monthlyGrowth',
            'dateFrom', 'dateTo',
            'maritalStatus', 'employmentStatus', 'universityStudent', 'universityId', 'studyDateFrom', 'studyDateTo',
            'byEmployment', 'universityStudentsCount', 'universityRows', 'universities'
        ));
    }

    public function membersExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $maritalStatus = trim((string) $request->string('marital_status'));
        $employmentStatus = trim((string) $request->string('employment_status'));
        $universityStudent = trim((string) $request->string('university_student'));
        $universityId = $request->integer('university_id') ?: null;
        $studyDateFrom = $request->filled('study_date_from') ? $request->string('study_date_from')->toString() : null;
        $studyDateTo = $request->filled('study_date_to') ? $request->string('study_date_to')->toString() : null;

        $members = Member::query()
            ->when($dateFrom, fn ($q) => $q->where('membership_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('membership_date', '<=', $dateTo))
            ->when($maritalStatus !== '', fn ($q) => $q->where('marital_status', $maritalStatus))
            ->when($employmentStatus !== '', fn ($q) => $q->where('employment_status', $employmentStatus))
            ->when($universityStudent !== '', function ($q) use ($universityStudent) {
                if ($universityStudent === 'yes') {
                    $q->where('is_university_student', true);
                } elseif ($universityStudent === 'no') {
                    $q->where('is_university_student', false);
                }
            })
            ->when($universityId, fn ($q) => $q->where('university_id', $universityId))
            ->when($studyDateFrom, fn ($q) => $q->where('university_start_date', '>=', $studyDateFrom))
            ->when($studyDateTo, fn ($q) => $q->where('university_end_date', '<=', $studyDateTo))
            ->with('university:id,name')
            ->orderBy('full_name')
            ->get([
                'full_name',
                'gender',
                'zone',
                'marital_status',
                'membership_date',
                'is_born_again',
                'is_baptized',
                'employment_status',
                'is_university_student',
                'university_id',
                'university_start_date',
                'university_end_date',
            ]);

        return response()->streamDownload(function () use ($members): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'name',
                'gender',
                'zone',
                'marital_status',
                'membership_date',
                'employment_status',
                'university_student',
                'university',
                'university_start_date',
                'university_end_date',
                'born_again',
                'baptized',
            ]);
            foreach ($members as $m) {
                fputcsv($handle, [
                    $m->full_name,
                    $m->gender,
                    $m->zone,
                    $m->marital_status,
                    optional($m->membership_date)->toDateString(),
                    $m->employment_status,
                    $m->is_university_student ? 'Yes' : 'No',
                    $m->university?->name,
                    optional($m->university_start_date)->toDateString(),
                    optional($m->university_end_date)->toDateString(),
                    $m->is_born_again ? 'Yes' : 'No',
                    $m->is_baptized ? 'Yes' : 'No',
                ]);
            }
            fclose($handle);
        }, 'members-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function finance(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $incomeQ = Income::query()->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('received_date', '<=', $dateTo));
        $totalIncome = (clone $incomeQ)->sum('amount');
        $incomeByType = (clone $incomeQ)->join('income_types', 'incomes.income_type_id', '=', 'income_types.id')
            ->select('income_types.type', DB::raw('SUM(incomes.amount) as total'))->groupBy('income_types.type')->orderByDesc('total')->get();

        $expQ = Expenditure::query()->when($dateFrom, fn ($q) => $q->where('expense_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('expense_date', '<=', $dateTo));
        $totalExpenditure = (clone $expQ)->sum('amount');
        $expByCategory = (clone $expQ)->select('expense_category', DB::raw('SUM(amount) as total'))->groupBy('expense_category')->orderByDesc('total')->get();

        $donQ = Donation::query()->when($dateFrom, fn ($q) => $q->where('donation_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('donation_date', '<=', $dateTo));
        $totalDonations = (clone $donQ)->sum('amount');
        $donByType = (clone $donQ)->select('type', DB::raw('SUM(amount) as total'))->groupBy('type')->orderByDesc('total')->get();

        $deptIncQ = DepartmentIncome::query()->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('received_date', '<=', $dateTo));
        $totalDeptIncome = (clone $deptIncQ)->sum('amount');
        $deptIncByDept = (clone $deptIncQ)->select('department', DB::raw('SUM(amount) as total'))->groupBy('department')->orderByDesc('total')->get();

        $deptExpQ = DepartmentExpense::query()->when($dateFrom, fn ($q) => $q->where('expense_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('expense_date', '<=', $dateTo));
        $totalDeptExpense = (clone $deptExpQ)->sum('amount');
        $deptExpByDept = (clone $deptExpQ)->select('department', DB::raw('SUM(amount) as total'))->groupBy('department')->orderByDesc('total')->get();

        $monthlyIncome = Income::query()
            ->select(DB::raw("DATE_FORMAT(received_date,'%Y-%m') as month"), DB::raw('SUM(amount) as total'))
            ->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('received_date', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('received_date', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month')->get();

        $netSurplus = $totalIncome + $totalDonations + $totalDeptIncome - $totalExpenditure - $totalDeptExpense;

        return view('reports.finance', compact(
            'totalIncome', 'totalExpenditure', 'totalDonations',
            'totalDeptIncome', 'totalDeptExpense', 'netSurplus',
            'incomeByType', 'expByCategory', 'donByType',
            'deptIncByDept', 'deptExpByDept', 'monthlyIncome',
            'dateFrom', 'dateTo'
        ));
    }

    public function financeExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $incomes = Income::query()->with('incomeType')->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('received_date', '<=', $dateTo))->get();
        $expenses = Expenditure::query()->when($dateFrom, fn ($q) => $q->where('expense_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('expense_date', '<=', $dateTo))->get();

        return response()->streamDownload(function () use ($incomes, $expenses): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['section', 'type/category', 'date', 'amount', 'reference', 'comment']);
            foreach ($incomes as $row) {
                fputcsv($handle, ['Income', $row->incomeType?->type ?? '—', $row->received_date, $row->amount, '', $row->comment]);
            }
            foreach ($expenses as $row) {
                fputcsv($handle, ['Expenditure', $row->expense_category, $row->expense_date, $row->amount, $row->reference_no, $row->comment]);
            }
            fclose($handle);
        }, 'finance-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function visitors(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = Visitor::query()
            ->when($dateFrom, fn ($q) => $q->where('first_visit_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('first_visit_date', '<=', $dateTo));

        $total = (clone $base)->count();
        $converted = (clone $base)->whereNotNull('converted_member_id')->count();
        $conversionRate = $total > 0 ? round($converted / $total * 100, 1) : 0;
        $byStatus = (clone $base)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $byGender = (clone $base)->select('gender', DB::raw('count(*) as total'))->groupBy('gender')->pluck('total', 'gender');

        $monthlyVisitors = Visitor::query()
            ->select(DB::raw("DATE_FORMAT(first_visit_date,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNotNull('first_visit_date')
            ->when($dateFrom, fn ($q) => $q->where('first_visit_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('first_visit_date', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('first_visit_date', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month')->get();

        $recentVisitors = (clone $base)->orderByDesc('first_visit_date')->limit(30)
            ->get(['id', 'full_name', 'gender', 'status', 'first_visit_date', 'invited_by', 'converted_member_id']);

        return view('reports.visitors', compact(
            'total', 'converted', 'conversionRate', 'byStatus', 'byGender',
            'monthlyVisitors', 'recentVisitors', 'dateFrom', 'dateTo'
        ));
    }

    public function visitorsExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $visitors = Visitor::query()
            ->when($dateFrom, fn ($q) => $q->where('first_visit_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('first_visit_date', '<=', $dateTo))
            ->orderByDesc('first_visit_date')
            ->get(['full_name', 'gender', 'phone', 'status', 'first_visit_date', 'invited_by', 'converted_member_id']);

        return response()->streamDownload(function () use ($visitors): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'gender', 'phone', 'status', 'first_visit', 'invited_by', 'converted']);
            foreach ($visitors as $v) {
                fputcsv($handle, [
                    $v->full_name, $v->gender, $v->phone, $v->status,
                    optional($v->first_visit_date)->toDateString(), $v->invited_by,
                    $v->converted_member_id ? 'Yes' : 'No',
                ]);
            }
            fclose($handle);
        }, 'visitors-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function childrenMinistry(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = \App\Models\ChildrenMinistry::query()
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'));

        $total = (clone $base)->count();
        $withLinkedParents = (clone $base)->whereNotNull('parent_member_id')->count();
        $bySex = (clone $base)->select('sex', DB::raw('count(*) as total'))->groupBy('sex')->pluck('total', 'sex');

        $recentChildren = (clone $base)->with('parentMember:id,full_name')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get(['id', 'first_name', 'middle_name', 'surname', 'sex', 'date_of_birth', 'parent_name', 'parent_contact', 'parent_member_id', 'created_at']);

        return view('reports.children-ministry', compact(
            'total', 'withLinkedParents', 'bySex',
            'recentChildren', 'dateFrom', 'dateTo'
        ));
    }

    public function childrenMinistryExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $children = \App\Models\ChildrenMinistry::query()
            ->with('parentMember:id,full_name')
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'))
            ->orderByDesc('created_at')
            ->get();

        return response()->streamDownload(function () use ($children): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['first_name', 'middle_name', 'surname', 'date_of_birth', 'sex', 'parent_name', 'parent_contact', 'linked_member', 'remarks', 'added_date']);
            foreach ($children as $child) {
                fputcsv($handle, [
                    $child->first_name,
                    $child->middle_name,
                    $child->surname,
                    optional($child->date_of_birth)->toDateString(),
                    $child->sex,
                    $child->parent_name,
                    $child->parent_contact,
                    $child->parentMember?->full_name ?? '—',
                    $child->remarks,
                    optional($child->created_at)->toDateString(),
                ]);
            }
            fclose($handle);
        }, 'children-ministry-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function discipleship(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = DiscipleshipParticipant::query()
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo));

        $total = (clone $base)->count();
        $registeredMembers = (clone $base)->whereNotNull('member_id')->count();
        $completed = (clone $base)->whereHas('stages', fn ($query) => $query->where('status', 'completed'), '=', 4)->count();
        $awarded = (clone $base)->whereNotNull('certificate_awarded_at')->count();
        $stageBreakdown = (clone $base)->with('stages')->get()->flatMap->stages
            ->groupBy('stage_number')
            ->map(fn ($stages) => $stages->countBy('status'));
        $participants = (clone $base)->with(['member:id,full_name,phone', 'stages' => fn ($query) => $query->orderBy('stage_number')])
            ->latest()->limit(50)->get();

        return view('reports.discipleship', compact('total', 'registeredMembers', 'completed', 'awarded', 'stageBreakdown', 'participants', 'dateFrom', 'dateTo'));
    }

    public function discipleshipExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);
        $participants = DiscipleshipParticipant::query()
            ->with(['member:id,full_name,phone', 'stages'])
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->latest()->get();

        return response()->streamDownload(function () use ($participants): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['participant', 'participant_type', 'phone', 'foundation_1', 'foundation_2', 'foundation_3', 'foundation_4', 'certificate_number', 'certificate_awarded_at', 'enrolled_at']);
            foreach ($participants as $participant) {
                fputcsv($handle, [
                    $participant->display_name,
                    $participant->member_id ? 'Registered member' : 'External participant',
                    $participant->member?->phone ?: $participant->external_phone,
                    ...collect([1, 2, 3, 4])->map(fn ($stage) => $participant->stages->firstWhere('stage_number', $stage)?->status ?? 'not_started')->all(),
                    $participant->certificate_number,
                    optional($participant->certificate_awarded_at)->toDateTimeString(),
                    optional($participant->created_at)->toDateString(),
                ]);
            }
            fclose($handle);
        }, 'discipleship-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pledges(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $pledgeQ = Pledge::query()->when($dateFrom, fn ($q) => $q->where('pledge_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('pledge_date', '<=', $dateTo));
        $totalPledged = (clone $pledgeQ)->sum('amount');
        $pledgeIds = (clone $pledgeQ)->pluck('id');
        $totalCollected = PledgePayment::query()->whereIn('pledge_id', $pledgeIds)->sum('amount');
        $outstanding = max(0, $totalPledged - $totalCollected);
        $fulfillmentRate = $totalPledged > 0 ? round($totalCollected / $totalPledged * 100, 1) : 0;

        $byType = (clone $pledgeQ)->select('pledge_type', DB::raw('SUM(amount) as total'), DB::raw('count(*) as count'))->groupBy('pledge_type')->orderByDesc('total')->get();

        $byZone = Pledge::query()
            ->join('members', 'pledges.member_id', '=', 'members.id')
            ->select('members.zone', DB::raw('SUM(pledges.amount) as total'), DB::raw('count(*) as count'))
            ->when($dateFrom, fn ($q) => $q->where('pledges.pledge_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('pledges.pledge_date', '<=', $dateTo))
            ->groupBy('members.zone')->orderByDesc('total')->get();

        $monthlyPledges = Pledge::query()
            ->select(DB::raw("DATE_FORMAT(pledge_date,'%Y-%m') as month"), DB::raw('SUM(amount) as total'))
            ->when($dateFrom, fn ($q) => $q->where('pledge_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('pledge_date', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('pledge_date', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month')->get();

        $recentPledges = (clone $pledgeQ)->with('payments')->orderByDesc('pledge_date')->limit(20)->get();

        return view('reports.pledges', compact(
            'totalPledged', 'totalCollected', 'outstanding', 'fulfillmentRate',
            'byType', 'byZone', 'monthlyPledges', 'recentPledges',
            'dateFrom', 'dateTo'
        ));
    }

    public function pledgesExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $pledges = Pledge::query()->with('payments')
            ->when($dateFrom, fn ($q) => $q->where('pledge_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('pledge_date', '<=', $dateTo))
            ->orderByDesc('pledge_date')->get();

        return response()->streamDownload(function () use ($pledges): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['pledger', 'type', 'pledged', 'collected', 'outstanding', 'pledge_date', 'due_date']);
            foreach ($pledges as $pledge) {
                $collected = $pledge->payments->sum('amount');
                fputcsv($handle, [
                    $pledge->pledger_name, $pledge->pledge_type, $pledge->amount,
                    $collected, max(0, $pledge->amount - $collected),
                    $pledge->pledge_date, $pledge->due_date,
                ]);
            }
            fclose($handle);
        }, 'pledges-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function followup(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = FollowUpTask::query()
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'));

        $total = (clone $base)->count();
        $open = (clone $base)->where('status', 'open')->count();
        $inProgress = (clone $base)->where('status', 'in_progress')->count();
        $completed = (clone $base)->where('status', 'completed')->count();
        $overdue = (clone $base)->where('status', '!=', 'completed')->where('due_date', '<', now()->toDateString())->count();
        $completionRate = $total > 0 ? round($completed / $total * 100, 1) : 0;

        $byAssignee = (clone $base)
            ->join('users', 'follow_up_tasks.assigned_to', '=', 'users.id')
            ->select(
                'users.full_name as name',
                DB::raw('count(*) as total'),
                DB::raw("SUM(CASE WHEN follow_up_tasks.status = 'completed' THEN 1 ELSE 0 END) as completed_count"),
                DB::raw("SUM(CASE WHEN follow_up_tasks.status != 'completed' AND follow_up_tasks.due_date < CURDATE() THEN 1 ELSE 0 END) as overdue_count"),
            )
            ->groupBy('users.full_name')->orderByDesc('total')->get();

        $byType = (clone $base)->select('task_type', DB::raw('count(*) as total'))->groupBy('task_type')->orderByDesc('total')->get();
        $byPriority = (clone $base)->select('priority', DB::raw('count(*) as total'))->groupBy('priority')->pluck('total', 'priority');

        $recentOverdue = FollowUpTask::query()->with('assignee:id,full_name')
            ->where('status', '!=', 'completed')->where('due_date', '<', now()->toDateString())
            ->orderBy('due_date')->limit(20)->get();

        return view('reports.followup', compact(
            'total', 'open', 'inProgress', 'completed', 'overdue',
            'completionRate', 'byAssignee', 'byType', 'byPriority',
            'recentOverdue', 'dateFrom', 'dateTo'
        ));
    }

    public function followupExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $tasks = FollowUpTask::query()->with('assignee:id,full_name')
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'))
            ->orderByDesc('created_at')->get();

        return response()->streamDownload(function () use ($tasks): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['task_type', 'priority', 'assigned_to', 'status', 'due_date', 'completed_at', 'notes']);
            foreach ($tasks as $task) {
                fputcsv($handle, [
                    $task->task_type, $task->priority, $task->assignee?->name,
                    $task->status, optional($task->due_date)->toDateString(),
                    optional($task->completed_at)->toDateTimeString(), $task->notes,
                ]);
            }
            fclose($handle);
        }, 'followup-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function communications(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = Communication::query()
            ->when($dateFrom, fn ($q) => $q->where('sent_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('sent_at', '<=', $dateTo . ' 23:59:59'));

        $total = (clone $base)->count();
        $sent = (clone $base)->where('status', 'sent')->count();
        $pending = (clone $base)->where('status', 'pending')->count();
        $failed = (clone $base)->where('status', 'failed')->count();

        $totalDeliveries = CommunicationDelivery::query()
            ->when($dateFrom, fn ($q) => $q->where('delivered_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('delivered_at', '<=', $dateTo . ' 23:59:59'))
            ->count();
        $deliveredCount = CommunicationDelivery::query()->where('delivery_status', 'delivered')
            ->when($dateFrom, fn ($q) => $q->where('delivered_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('delivered_at', '<=', $dateTo . ' 23:59:59'))
            ->count();
        $deliveryRate = $totalDeliveries > 0 ? round($deliveredCount / $totalDeliveries * 100, 1) : 0;

        $byChannel = (clone $base)->select('channel', DB::raw('count(*) as total'))->groupBy('channel')->orderByDesc('total')->get();
        $byAudienceType = (clone $base)->select('audience_type', DB::raw('count(*) as total'))->groupBy('audience_type')->orderByDesc('total')->get();

        $monthlyTrend = Communication::query()
            ->select(DB::raw("DATE_FORMAT(sent_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNotNull('sent_at')
            ->when($dateFrom, fn ($q) => $q->where('sent_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('sent_at', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('sent_at', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month')->get();

        $recentComms = Communication::query()->with('creator:id,full_name')
            ->withCount(['deliveries', 'deliveries as delivered_count' => fn ($q) => $q->where('delivery_status', 'delivered')])
            ->when($dateFrom, fn ($q) => $q->where('sent_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('sent_at', '<=', $dateTo . ' 23:59:59'))
            ->orderByDesc('sent_at')->limit(20)->get();

        return view('reports.communications', compact(
            'total', 'sent', 'pending', 'failed',
            'totalDeliveries', 'deliveredCount', 'deliveryRate',
            'byChannel', 'byAudienceType', 'monthlyTrend', 'recentComms',
            'dateFrom', 'dateTo'
        ));
    }

    public function communicationsExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $comms = Communication::query()->with('creator:id,full_name')
            ->withCount(['deliveries', 'deliveries as delivered_count' => fn ($q) => $q->where('delivery_status', 'delivered')])
            ->when($dateFrom, fn ($q) => $q->where('sent_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('sent_at', '<=', $dateTo . ' 23:59:59'))
            ->orderByDesc('sent_at')->get();

        return response()->streamDownload(function () use ($comms): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['subject', 'channel', 'audience_type', 'status', 'sent_at', 'created_by', 'deliveries', 'delivered']);
            foreach ($comms as $c) {
                fputcsv($handle, [
                    $c->subject, $c->channel, $c->audience_type, $c->status,
                    optional($c->sent_at)->toDateTimeString(),
                    $c->creator?->name ?? '—',
                    $c->deliveries_count, $c->delivered_count,
                ]);
            }
            fclose($handle);
        }, 'communications-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function extractDateRange(Request $request): array
    {
        return [
            'dateFrom' => $request->filled('date_from') ? $request->string('date_from')->toString() : null,
            'dateTo' => $request->filled('date_to') ? $request->string('date_to')->toString() : null,
        ];
    }

    private function departmentReportRows(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection
    {
        return Department::query()->withCount('memberships')->get()
            ->map(function (Department $department) use ($dateFrom, $dateTo): array {
                $attendanceCount = AttendanceRecord::query()
                    ->where('department_id', $department->id)
                    ->when($dateFrom, fn ($q) => $q->where('recorded_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q) => $q->where('recorded_at', '<=', $dateTo . ' 23:59:59'))
                    ->count();

                return [
                    'name' => $department->name,
                    'members' => $department->memberships_count,
                    'attendance' => $attendanceCount,
                    'attendance_rate' => $department->memberships_count > 0
                        ? round($attendanceCount / $department->memberships_count, 1)
                        : 0,
                ];
            })
            ->sortByDesc('attendance')->values();
    }

    private function zoneReportRows(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection
    {
        return Zone::query()->withCount('memberships')->with('leader:id,full_name')->get()
            ->map(function (Zone $zone) use ($dateFrom, $dateTo): array {
                $attendanceCount = AttendanceRecord::query()
                    ->where('zone', $zone->name)
                    ->when($dateFrom, fn ($q) => $q->where('recorded_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q) => $q->where('recorded_at', '<=', $dateTo . ' 23:59:59'))
                    ->count();

                return [
                    'name' => $zone->name,
                    'leader' => $zone->leader?->full_name ?? '—',
                    'members' => $zone->memberships_count,
                    'attendance' => $attendanceCount,
                    'attendance_rate' => $zone->memberships_count > 0
                        ? round($attendanceCount / $zone->memberships_count, 1)
                        : 0,
                ];
            })
            ->sortByDesc('attendance')->values();
    }

    private function groupReportRows(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection
    {
        return Group::query()
            ->withCount([
                'memberships as total_count' => fn ($q) => $q
                    ->when($dateFrom, fn ($q2) => $q2->whereDate('joined_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q2) => $q2->whereDate('joined_at', '<=', $dateTo)),
                'memberships as registered_count' => fn ($q) => $q
                    ->whereNotNull('member_id')
                    ->when($dateFrom, fn ($q2) => $q2->whereDate('joined_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q2) => $q2->whereDate('joined_at', '<=', $dateTo)),
                'memberships as guest_count' => fn ($q) => $q
                    ->whereNull('member_id')
                    ->when($dateFrom, fn ($q2) => $q2->whereDate('joined_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q2) => $q2->whereDate('joined_at', '<=', $dateTo)),
                'memberships as leaders_count' => fn ($q) => $q
                    ->where('role', 'leader')
                    ->when($dateFrom, fn ($q2) => $q2->whereDate('joined_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q2) => $q2->whereDate('joined_at', '<=', $dateTo)),
                'memberships as coordinators_count' => fn ($q) => $q
                    ->where('role', 'coordinator')
                    ->when($dateFrom, fn ($q2) => $q2->whereDate('joined_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q2) => $q2->whereDate('joined_at', '<=', $dateTo)),
                'memberships as members_count' => fn ($q) => $q
                    ->where('role', 'member')
                    ->when($dateFrom, fn ($q2) => $q2->whereDate('joined_at', '>=', $dateFrom))
                    ->when($dateTo, fn ($q2) => $q2->whereDate('joined_at', '<=', $dateTo)),
            ])
            ->orderByDesc('total_count')
            ->orderBy('name')
            ->get(['id', 'name', 'icon', 'color'])
            ->map(fn (Group $group): array => [
                'name' => $group->name,
                'icon' => $group->icon,
                'color' => $group->color,
                'registered' => $group->registered_count,
                'guests' => $group->guest_count,
                'leaders' => $group->leaders_count,
                'coordinators' => $group->coordinators_count,
                'members' => $group->members_count,
                'total' => $group->total_count,
            ])
            ->values();
    }
}
