<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\Department;
use App\Models\DepartmentExpense;
use App\Models\DepartmentIncome;
use App\Models\Donation;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Expenditure;
use App\Models\FollowUpTask;
use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\PastoralCase;
use App\Models\Payroll;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\University;
use App\Models\Visitor;
use App\Models\VolunteerAssignment;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    // ── Hub ───────────────────────────────────────────────────────────────

    public function index(): View
    {
        return view('reports.index');
    }

    // ── Departments ───────────────────────────────────────────────────────

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
            fputcsv($handle, ['department', 'members', 'attendance_records', 'completed_assignments', 'attendance_rate']);
            foreach ($departments as $row) {
                fputcsv($handle, [$row['name'], $row['members'], $row['attendance'], $row['completed_assignments'], $row['attendance_rate']]);
            }
            fclose($handle);
        }, 'department-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Zones ─────────────────────────────────────────────────────────────

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
            fputcsv($handle, ['zone', 'leader', 'members', 'families', 'attendance_records', 'attendance_rate']);
            foreach ($zones as $row) {
                fputcsv($handle, [$row['name'], $row['leader'], $row['members'], $row['families'], $row['attendance'], $row['attendance_rate']]);
            }
            fclose($handle);
        }, 'zone-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Groups ───────────────────────────────────────────────────────────

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

    // ── Events ────────────────────────────────────────────────────────────

    public function events(Request $request): View
    {
        $filters = $this->extractAllFilters($request);
        $events  = $this->eventReportQuery($filters)->paginate(15)->withQueryString();

        return view('reports.events', array_merge($filters, [
            'events'      => $events,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'zones'       => Zone::query()->orderBy('name')->get(['id', 'name']),
        ]));
    }

    public function eventsExport(Request $request): StreamedResponse
    {
        $filters = $this->extractAllFilters($request);
        $events  = $this->eventReportQuery($filters)->get();

        return response()->streamDownload(function () use ($events): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['event', 'event_type', 'date', 'status', 'registrations', 'volunteer_assignments']);
            foreach ($events as $event) {
                fputcsv($handle, [
                    $event->title,
                    $event->event_type,
                    optional($event->start_date)->toDateString(),
                    $event->status,
                    $event->registrations_count,
                    $event->volunteer_assignments_count,
                ]);
            }
            fclose($handle);
        }, 'event-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Volunteers ────────────────────────────────────────────────────────

    public function volunteers(Request $request): View
    {
        $filters     = $this->extractAllFilters($request);
        $assignments = $this->volunteerReportQuery($filters)->orderByDesc('id')->paginate(20)->withQueryString();
        $summaryBase = $this->volunteerReportQuery($filters);

        $summary = [
            'total'     => (clone $summaryBase)->count(),
            'assigned'  => (clone $summaryBase)->where('status', 'assigned')->count(),
            'confirmed' => (clone $summaryBase)->where('status', 'confirmed')->count(),
            'completed' => (clone $summaryBase)->where('status', 'completed')->count(),
            'cancelled' => (clone $summaryBase)->where('status', 'cancelled')->count(),
        ];

        $topVolunteers = VolunteerAssignment::query()
            ->join('members', 'volunteer_assignments.member_id', '=', 'members.id')
            ->select(
                'members.full_name',
                DB::raw('count(*) as total'),
                DB::raw("SUM(CASE WHEN volunteer_assignments.status = 'completed' THEN 1 ELSE 0 END) as completed_count")
            )
            ->when($filters['departmentId'], fn ($q, $id) => $q->where('volunteer_assignments.department_id', $id))
            ->when($filters['zone'] !== '', fn ($q) => $q->where('members.zone', $filters['zone']))
            ->when($filters['dateFrom'], fn ($q, $d) => $q->whereHas('event', fn ($q2) => $q2->where('start_date', '>=', $d)))
            ->when($filters['dateTo'],   fn ($q, $d) => $q->whereHas('event', fn ($q2) => $q2->where('start_date', '<=', $d)))
            ->groupBy('members.id', 'members.full_name')
            ->orderByDesc('completed_count')->limit(10)->get();

        return view('reports.volunteers', array_merge($filters, [
            'assignments'   => $assignments,
            'summary'       => $summary,
            'topVolunteers' => $topVolunteers,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'zones'       => Zone::query()->orderBy('name')->get(['id', 'name']),
        ]));
    }

    public function volunteersExport(Request $request): StreamedResponse
    {
        $filters     = $this->extractAllFilters($request);
        $assignments = $this->volunteerReportQuery($filters)
            ->with(['member:id,full_name', 'event:id,title', 'department:id,name'])
            ->orderByDesc('id')->get();

        return response()->streamDownload(function () use ($assignments): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['volunteer', 'role', 'event', 'department', 'status']);
            foreach ($assignments as $a) {
                fputcsv($handle, [$a->member?->full_name, $a->role, $a->event?->title, $a->department?->name, $a->status]);
            }
            fclose($handle);
        }, 'volunteer-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Members ───────────────────────────────────────────────────────────

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
            ->when($dateTo,   fn ($q) => $q->where('membership_date', '<=', $dateTo))
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

        $total      = (clone $base)->count();
        $marriedCount = (clone $base)->where('marital_status', 'Married')->count();
        $singleCount = (clone $base)->where('marital_status', 'Single')->count();
        $bornAgain  = (clone $base)->where('is_born_again', true)->count();
        $baptized   = (clone $base)->where('is_baptized', true)->count();
        $holySpirit = (clone $base)->where('holy_spirit_baptised', true)->count();

        $byGender  = (clone $base)->select('gender', DB::raw('count(*) as total'))->groupBy('gender')->pluck('total', 'gender');
        $byMarital = (clone $base)->select('marital_status', DB::raw('count(*) as total'))->groupBy('marital_status')->pluck('total', 'marital_status');
        $byZone    = (clone $base)->select('zone', DB::raw('count(*) as total'))->groupBy('zone')->orderByDesc('total')->get();
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
            ->when($dateTo,   fn ($q) => $q->where('membership_date', '<=', $dateTo))
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
            ->when($dateTo,   fn ($q) => $q->where('membership_date', '<=', $dateTo))
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
                    $m->is_baptized   ? 'Yes' : 'No',
                ]);
            }
            fclose($handle);
        }, 'members-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Finance ───────────────────────────────────────────────────────────

    public function finance(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        // General income
        $incomeQ       = Income::query()->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('received_date', '<=', $dateTo));
        $totalIncome   = (clone $incomeQ)->sum('amount');
        $incomeByType  = (clone $incomeQ)->join('income_types', 'incomes.income_type_id', '=', 'income_types.id')
            ->select('income_types.type', DB::raw('SUM(incomes.amount) as total'))->groupBy('income_types.type')->orderByDesc('total')->get();

        // Expenditure
        $expQ             = Expenditure::query()->when($dateFrom, fn ($q) => $q->where('expense_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('expense_date', '<=', $dateTo));
        $totalExpenditure = (clone $expQ)->sum('amount');
        $expByCategory    = (clone $expQ)->select('expense_category', DB::raw('SUM(amount) as total'))->groupBy('expense_category')->orderByDesc('total')->get();

        // Donations
        $donQ          = Donation::query()->when($dateFrom, fn ($q) => $q->where('donation_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('donation_date', '<=', $dateTo));
        $totalDonations = (clone $donQ)->sum('amount');
        $donByType     = (clone $donQ)->select('type', DB::raw('SUM(amount) as total'))->groupBy('type')->orderByDesc('total')->get();

        // Department income & expense
        $deptIncQ        = DepartmentIncome::query()->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('received_date', '<=', $dateTo));
        $totalDeptIncome = (clone $deptIncQ)->sum('amount');
        $deptIncByDept   = (clone $deptIncQ)->select('department', DB::raw('SUM(amount) as total'))->groupBy('department')->orderByDesc('total')->get();

        $deptExpQ         = DepartmentExpense::query()->when($dateFrom, fn ($q) => $q->where('expense_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('expense_date', '<=', $dateTo));
        $totalDeptExpense = (clone $deptExpQ)->sum('amount');
        $deptExpByDept    = (clone $deptExpQ)->select('department', DB::raw('SUM(amount) as total'))->groupBy('department')->orderByDesc('total')->get();

        // Monthly income trend
        $monthlyIncome = Income::query()
            ->select(DB::raw("DATE_FORMAT(received_date,'%Y-%m') as month"), DB::raw('SUM(amount) as total'))
            ->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('received_date', '<=', $dateTo))
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

        $incomes  = Income::query()->with('incomeType')->when($dateFrom, fn ($q) => $q->where('received_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('received_date', '<=', $dateTo))->get();
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

    // ── Visitors ──────────────────────────────────────────────────────────

    public function visitors(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = Visitor::query()
            ->when($dateFrom, fn ($q) => $q->where('first_visit_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('first_visit_date', '<=', $dateTo));

        $total          = (clone $base)->count();
        $converted      = (clone $base)->whereNotNull('converted_member_id')->count();
        $conversionRate = $total > 0 ? round($converted / $total * 100, 1) : 0;
        $byStatus       = (clone $base)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $byGender       = (clone $base)->select('gender', DB::raw('count(*) as total'))->groupBy('gender')->pluck('total', 'gender');

        $monthlyVisitors = Visitor::query()
            ->select(DB::raw("DATE_FORMAT(first_visit_date,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNotNull('first_visit_date')
            ->when($dateFrom, fn ($q) => $q->where('first_visit_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('first_visit_date', '<=', $dateTo))
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
            ->when($dateTo,   fn ($q) => $q->where('first_visit_date', '<=', $dateTo))
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

    // ── Pledges ───────────────────────────────────────────────────────────

    public function pledges(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $pledgeQ       = Pledge::query()->when($dateFrom, fn ($q) => $q->where('pledge_date', '>=', $dateFrom))->when($dateTo, fn ($q) => $q->where('pledge_date', '<=', $dateTo));
        $totalPledged  = (clone $pledgeQ)->sum('amount');
        $pledgeIds     = (clone $pledgeQ)->pluck('id');
        $totalCollected = PledgePayment::query()->whereIn('pledge_id', $pledgeIds)->sum('amount');
        $outstanding    = max(0, $totalPledged - $totalCollected);
        $fulfillmentRate = $totalPledged > 0 ? round($totalCollected / $totalPledged * 100, 1) : 0;

        $byType = (clone $pledgeQ)->select('pledge_type', DB::raw('SUM(amount) as total'), DB::raw('count(*) as count'))->groupBy('pledge_type')->orderByDesc('total')->get();

        $byZone = Pledge::query()
            ->join('members', 'pledges.member_id', '=', 'members.id')
            ->select('members.zone', DB::raw('SUM(pledges.amount) as total'), DB::raw('count(*) as count'))
            ->when($dateFrom, fn ($q) => $q->where('pledges.pledge_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('pledges.pledge_date', '<=', $dateTo))
            ->groupBy('members.zone')->orderByDesc('total')->get();

        $monthlyPledges = Pledge::query()
            ->select(DB::raw("DATE_FORMAT(pledge_date,'%Y-%m') as month"), DB::raw('SUM(amount) as total'))
            ->when($dateFrom, fn ($q) => $q->where('pledge_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('pledge_date', '<=', $dateTo))
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
            ->when($dateTo,   fn ($q) => $q->where('pledge_date', '<=', $dateTo))
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

    // ── Follow-Up ─────────────────────────────────────────────────────────

    public function followup(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = FollowUpTask::query()
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'));

        $total      = (clone $base)->count();
        $open       = (clone $base)->where('status', 'open')->count();
        $inProgress = (clone $base)->where('status', 'in_progress')->count();
        $completed  = (clone $base)->where('status', 'completed')->count();
        $overdue    = (clone $base)->where('status', '!=', 'completed')->where('due_date', '<', now()->toDateString())->count();
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

        $byType     = (clone $base)->select('task_type', DB::raw('count(*) as total'))->groupBy('task_type')->orderByDesc('total')->get();
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
            ->when($dateTo,   fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'))
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

    // ── Pastoral Care ─────────────────────────────────────────────────────

    public function pastoral(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = PastoralCase::query()
            ->when($dateFrom, fn ($q) => $q->where('opened_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('opened_at', '<=', $dateTo . ' 23:59:59'));

        $total    = (clone $base)->count();
        $open     = (clone $base)->where('status', 'open')->count();
        $closed   = (clone $base)->whereIn('status', ['closed', 'answered'])->count();
        $inProgress = (clone $base)->where('status', 'in_progress')->count();

        $byPriority = (clone $base)->select('priority', DB::raw('count(*) as total'))->groupBy('priority')->pluck('total', 'priority');
        $byType     = (clone $base)->select('case_type', DB::raw('count(*) as total'))->groupBy('case_type')->orderByDesc('total')->get();

        $byAssignee = (clone $base)
            ->join('leaders', 'pastoral_cases.assigned_to', '=', 'leaders.id')
            ->select(
                'leaders.full_name',
                DB::raw('count(*) as total'),
                DB::raw("SUM(CASE WHEN pastoral_cases.status IN ('closed', 'answered') THEN 1 ELSE 0 END) as resolved_count")
            )
            ->groupBy('leaders.full_name')->orderByDesc('total')->get();

        $monthlyTrend = PastoralCase::query()
            ->select(DB::raw("DATE_FORMAT(opened_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNotNull('opened_at')
            ->when($dateFrom, fn ($q) => $q->where('opened_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('opened_at', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('opened_at', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month')->get();

        $recentOpen = PastoralCase::query()->with(['member:id,full_name', 'assignee:id,full_name'])
            ->whereNotIn('status', ['closed', 'answered'])
            ->when($dateFrom, fn ($q) => $q->where('opened_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('opened_at', '<=', $dateTo . ' 23:59:59'))
            ->orderByDesc('opened_at')->limit(20)->get();

        return view('reports.pastoral', compact(
            'total', 'open', 'closed', 'inProgress',
            'byPriority', 'byType', 'byAssignee', 'monthlyTrend',
            'recentOpen', 'dateFrom', 'dateTo'
        ));
    }

    public function pastoralExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $cases = PastoralCase::query()->with(['member:id,full_name', 'assignee:id,full_name'])
            ->when($dateFrom, fn ($q) => $q->where('opened_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('opened_at', '<=', $dateTo . ' 23:59:59'))
            ->orderByDesc('opened_at')->get();

        return response()->streamDownload(function () use ($cases): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['member', 'case_type', 'priority', 'status', 'assigned_to', 'opened_at', 'closed_at', 'summary']);
            foreach ($cases as $c) {
                fputcsv($handle, [
                    $c->member?->full_name ?? '—', $c->case_type, $c->priority, $c->status,
                    $c->assignee?->full_name ?? '—',
                    optional($c->opened_at)->toDateString(),
                    optional($c->closed_at)->toDateString(),
                    $c->summary,
                ]);
            }
            fclose($handle);
        }, 'pastoral-care-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Communications ────────────────────────────────────────────────────

    public function communications(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = Communication::query()
            ->when($dateFrom, fn ($q) => $q->where('sent_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('sent_at', '<=', $dateTo . ' 23:59:59'));

        $total    = (clone $base)->count();
        $sent     = (clone $base)->where('status', 'sent')->count();
        $pending  = (clone $base)->where('status', 'pending')->count();
        $failed   = (clone $base)->where('status', 'failed')->count();

        $totalDeliveries  = CommunicationDelivery::query()
            ->when($dateFrom, fn ($q) => $q->where('delivered_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('delivered_at', '<=', $dateTo . ' 23:59:59'))
            ->count();
        $deliveredCount   = CommunicationDelivery::query()->where('delivery_status', 'delivered')
            ->when($dateFrom, fn ($q) => $q->where('delivered_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('delivered_at', '<=', $dateTo . ' 23:59:59'))
            ->count();
        $deliveryRate = $totalDeliveries > 0 ? round($deliveredCount / $totalDeliveries * 100, 1) : 0;

        $byChannel      = (clone $base)->select('channel', DB::raw('count(*) as total'))->groupBy('channel')->orderByDesc('total')->get();
        $byAudienceType = (clone $base)->select('audience_type', DB::raw('count(*) as total'))->groupBy('audience_type')->orderByDesc('total')->get();

        $monthlyTrend = Communication::query()
            ->select(DB::raw("DATE_FORMAT(sent_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNotNull('sent_at')
            ->when($dateFrom, fn ($q) => $q->where('sent_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('sent_at', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('sent_at', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month')->get();

        $recentComms = Communication::query()->with('creator:id,full_name')
            ->withCount(['deliveries', 'deliveries as delivered_count' => fn ($q) => $q->where('delivery_status', 'delivered')])
            ->when($dateFrom, fn ($q) => $q->where('sent_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('sent_at', '<=', $dateTo . ' 23:59:59'))
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
            ->when($dateTo,   fn ($q) => $q->where('sent_at', '<=', $dateTo . ' 23:59:59'))
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

    // ── Payroll ───────────────────────────────────────────────────────────

    public function payroll(Request $request): View
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $base = Payroll::query()
            ->when($dateFrom, fn ($q) => $q->where('payment_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('payment_date', '<=', $dateTo));

        $totalSalary    = (clone $base)->sum('salary');
        $totalNetSalary = (clone $base)->sum('net_salary');
        $totalPaye      = (clone $base)->sum('paye');
        $totalPaid      = (clone $base)->sum('paid_amount');

        $byEmployee = Payroll::query()
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->select(
                'employees.name',
                'employees.designation',
                DB::raw('count(*) as pay_periods'),
                DB::raw('SUM(payrolls.salary) as total_gross'),
                DB::raw('SUM(payrolls.net_salary) as total_net'),
                DB::raw('SUM(payrolls.paye) as total_paye'),
                DB::raw('SUM(payrolls.paid_amount) as total_paid')
            )
            ->when($dateFrom, fn ($q) => $q->where('payrolls.payment_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('payrolls.payment_date', '<=', $dateTo))
            ->groupBy('employees.id', 'employees.name', 'employees.designation')
            ->orderByDesc('total_gross')->get();

        $monthlyPayroll = Payroll::query()
            ->select(DB::raw("DATE_FORMAT(payment_date,'%Y-%m') as month"), DB::raw('SUM(salary) as total_gross'), DB::raw('SUM(net_salary) as total_net'))
            ->when($dateFrom, fn ($q) => $q->where('payment_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('payment_date', '<=', $dateTo))
            ->when(! $dateFrom && ! $dateTo, fn ($q) => $q->where('payment_date', '>=', now()->subMonths(11)->startOfMonth()))
            ->groupBy('month')->orderBy('month')->get();

        $recentPayrolls = Payroll::query()->with('employee:id,name,designation')
            ->when($dateFrom, fn ($q) => $q->where('payment_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('payment_date', '<=', $dateTo))
            ->orderByDesc('payment_date')->limit(30)->get();

        return view('reports.payroll', compact(
            'totalSalary', 'totalNetSalary', 'totalPaye', 'totalPaid',
            'byEmployee', 'monthlyPayroll', 'recentPayrolls',
            'dateFrom', 'dateTo'
        ));
    }

    public function payrollExport(Request $request): StreamedResponse
    {
        ['dateFrom' => $dateFrom, 'dateTo' => $dateTo] = $this->extractDateRange($request);

        $payrolls = Payroll::query()->with('employee:id,name,designation')
            ->when($dateFrom, fn ($q) => $q->where('payment_date', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('payment_date', '<=', $dateTo))
            ->orderByDesc('payment_date')->get();

        return response()->streamDownload(function () use ($payrolls): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['employee', 'designation', 'payment_date', 'method', 'gross_salary', 'tax_percent', 'paye', 'net_salary', 'paid_amount']);
            foreach ($payrolls as $p) {
                fputcsv($handle, [
                    $p->employee?->name ?? '—', $p->employee?->designation ?? '—',
                    optional($p->payment_date)->toDateString(), $p->method,
                    $p->salary, $p->tax_percent, $p->paye, $p->net_salary, $p->paid_amount,
                ]);
            }
            fclose($handle);
        }, 'payroll-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function eventReportQuery(array $filters)
    {
        return Event::query()
            ->withCount(['registrations', 'volunteerAssignments'])
            ->orderByDesc('start_date')
            ->when($filters['departmentId'], fn ($q, $id) => $q->whereHas('volunteerAssignments', fn ($q2) => $q2->where('department_id', $id)))
            ->when($filters['zone'] !== '', fn ($q) => $q->whereHas('registrations.member', fn ($q2) => $q2->where('zone', $filters['zone'])))
            ->when($filters['dateFrom'], fn ($q, $d) => $q->where('start_date', '>=', $d))
            ->when($filters['dateTo'],   fn ($q, $d) => $q->where('start_date', '<=', $d));
    }

    private function volunteerReportQuery(array $filters)
    {
        return VolunteerAssignment::query()
            ->with(['member:id,full_name', 'event:id,title,start_date', 'department:id,name'])
            ->when($filters['departmentId'], fn ($q, $id) => $q->where('department_id', $id))
            ->when($filters['zone'] !== '', fn ($q) => $q->whereHas('member', fn ($q2) => $q2->where('zone', $filters['zone'])))
            ->when($filters['dateFrom'], fn ($q, $d) => $q->whereHas('event', fn ($q2) => $q2->where('start_date', '>=', $d)))
            ->when($filters['dateTo'],   fn ($q, $d) => $q->whereHas('event', fn ($q2) => $q2->where('start_date', '<=', $d)));
    }

    private function extractDateRange(Request $request): array
    {
        return [
            'dateFrom' => $request->filled('date_from') ? $request->string('date_from')->toString() : null,
            'dateTo'   => $request->filled('date_to')   ? $request->string('date_to')->toString()   : null,
        ];
    }

    private function extractFilters(Request $request): array
    {
        return [
            'departmentId' => $request->integer('department_id') ?: null,
            'zone'         => trim((string) $request->string('zone')),
        ];
    }

    private function extractAllFilters(Request $request): array
    {
        return array_merge($this->extractFilters($request), $this->extractDateRange($request));
    }

    private function departmentReportRows(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection
    {
        return Department::query()->withCount('memberships')->get()
            ->map(function (Department $department) use ($dateFrom, $dateTo): array {
                $attendanceCount = AttendanceRecord::query()
                    ->where('department_id', $department->id)
                    ->when($dateFrom, fn ($q) => $q->where('recorded_at', '>=', $dateFrom))
                    ->when($dateTo,   fn ($q) => $q->where('recorded_at', '<=', $dateTo . ' 23:59:59'))
                    ->count();

                $completedAssignments = VolunteerAssignment::query()
                    ->where('department_id', $department->id)->where('status', 'completed')
                    ->when($dateFrom || $dateTo, fn ($q) => $q->whereHas('event', function ($q2) use ($dateFrom, $dateTo) {
                        $q2->when($dateFrom, fn ($q3) => $q3->where('start_date', '>=', $dateFrom))
                           ->when($dateTo,   fn ($q3) => $q3->where('start_date', '<=', $dateTo));
                    }))->count();

                return [
                    'name'                  => $department->name,
                    'members'               => $department->memberships_count,
                    'attendance'            => $attendanceCount,
                    'completed_assignments' => $completedAssignments,
                    'attendance_rate'       => $department->memberships_count > 0
                        ? round($attendanceCount / $department->memberships_count, 1) : 0,
                ];
            })
            ->sortByDesc('attendance')->values();
    }

    private function zoneReportRows(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection
    {
        // Pre-compute family counts per zone to avoid N+1
        $familyCountByZone = DB::table('families')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')->pluck('total', 'zone');

        return Zone::query()->withCount('memberships')->with('leader:id,full_name')->get()
            ->map(function (Zone $zone) use ($dateFrom, $dateTo, $familyCountByZone): array {
                $attendanceCount = AttendanceRecord::query()
                    ->where('zone', $zone->name)
                    ->when($dateFrom, fn ($q) => $q->where('recorded_at', '>=', $dateFrom))
                    ->when($dateTo,   fn ($q) => $q->where('recorded_at', '<=', $dateTo . ' 23:59:59'))
                    ->count();

                return [
                    'name'            => $zone->name,
                    'leader'          => $zone->leader?->full_name ?? '—',
                    'members'         => $zone->memberships_count,
                    'families'        => $familyCountByZone->get($zone->name, 0),
                    'attendance'      => $attendanceCount,
                    'attendance_rate' => $zone->memberships_count > 0
                        ? round($attendanceCount / $zone->memberships_count, 1) : 0,
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
