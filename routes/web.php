<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExecutiveDashboardController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberTimelineController;
use App\Http\Controllers\PastoralCareController;
use App\Http\Controllers\PrayerRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScorecardController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->hasPermission('dashboard.read')
            ? redirect()->route('dashboard.index')
            : redirect()->route('attendance.scan');
    }
    return redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

// Public QR self check-in (signed URL — no auth required)
Route::get('/checkin', [AttendanceController::class, 'checkin'])->name('attendance.checkin');
Route::post('/checkin', [AttendanceController::class, 'storeCheckin'])->name('attendance.checkin.store');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.read')
        ->name('dashboard.index');
    Route::get('/dashboard/executive', ExecutiveDashboardController::class)
        ->middleware('permission:dashboard.admin_kpis')
        ->name('dashboard.executive');

    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware('permission:users.read')
        ->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->middleware('permission:users.update')
        ->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('users.destroy');

    Route::get('/members/export', [MemberController::class, 'export'])
        ->middleware('permission:members.export')
        ->name('members.export');
    Route::post('/members/import', [MemberController::class, 'import'])
        ->middleware('permission:members.import')
        ->name('members.import');
    Route::get('/members/create', [MemberController::class, 'create'])
        ->middleware('permission:members.create')
        ->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])
        ->middleware('permission:members.create')
        ->name('members.store');
    Route::get('/members/{member}/edit', [MemberController::class, 'edit'])
        ->middleware('permission:members.update')
        ->name('members.edit');
    Route::put('/members/{member}', [MemberController::class, 'update'])
        ->middleware('permission:members.update')
        ->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])
        ->middleware('permission:members.delete')
        ->name('members.destroy');
    Route::get('/members/{member}/timeline', [MemberTimelineController::class, 'show'])
        ->middleware('permission:member_timeline.read')
        ->name('members.timeline');
    Route::resource('members', MemberController::class)
        ->middleware('permission:members.read')
        ->only(['index', 'show']);

    Route::get('/families/export', [FamilyController::class, 'export'])
        ->middleware('permission:families.export')
        ->name('families.export');
    Route::post('/families/import', [FamilyController::class, 'import'])
        ->middleware('permission:families.import')
        ->name('families.import');
    Route::get('/families/create', [FamilyController::class, 'create'])
        ->middleware('permission:families.create')
        ->name('families.create');
    Route::post('/families', [FamilyController::class, 'store'])
        ->middleware('permission:families.create')
        ->name('families.store');
    Route::get('/families/{family}/edit', [FamilyController::class, 'edit'])
        ->middleware('permission:families.update')
        ->name('families.edit');
    Route::put('/families/{family}', [FamilyController::class, 'update'])
        ->middleware('permission:families.update')
        ->name('families.update');
    Route::delete('/families/{family}', [FamilyController::class, 'destroy'])
        ->middleware('permission:families.delete')
        ->name('families.destroy');
    Route::resource('families', FamilyController::class)
        ->middleware('permission:families.read')
        ->only(['index', 'show']);

    Route::get('/departments/create', [DepartmentController::class, 'create'])
        ->middleware('permission:departments.create')
        ->name('departments.create');
    Route::post('/departments', [DepartmentController::class, 'store'])
        ->middleware('permission:departments.create')
        ->name('departments.store');
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])
        ->middleware('permission:departments.update')
        ->name('departments.edit');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])
        ->middleware('permission:departments.update')
        ->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])
        ->middleware('permission:departments.delete')
        ->name('departments.destroy');
    Route::resource('departments', DepartmentController::class)
        ->middleware('permission:departments.read')
        ->only(['index', 'show']);
    Route::post('/departments/{department}/members', [DepartmentController::class, 'storeMember'])
        ->middleware('permission:departments.update')
        ->name('departments.members.store');
    Route::delete('/departments/{department}/members/{membership}', [DepartmentController::class, 'destroyMember'])
        ->middleware('permission:departments.update')
        ->name('departments.members.destroy');

    Route::get('/zones/create', [ZoneController::class, 'create'])
        ->middleware('permission:zones.create')
        ->name('zones.create');
    Route::post('/zones', [ZoneController::class, 'store'])
        ->middleware('permission:zones.create')
        ->name('zones.store');
    Route::get('/zones/{zone}/edit', [ZoneController::class, 'edit'])
        ->middleware('permission:zones.update')
        ->name('zones.edit');
    Route::put('/zones/{zone}', [ZoneController::class, 'update'])
        ->middleware('permission:zones.update')
        ->name('zones.update');
    Route::delete('/zones/{zone}', [ZoneController::class, 'destroy'])
        ->middleware('permission:zones.delete')
        ->name('zones.destroy');
    Route::resource('zones', ZoneController::class)
        ->middleware('permission:zones.read')
        ->only(['index', 'show']);
    Route::post('/zones/{zone}/members', [ZoneController::class, 'storeMember'])
        ->middleware('permission:zones.update')
        ->name('zones.members.store');
    Route::delete('/zones/{zone}/members/{membership}', [ZoneController::class, 'destroyMember'])
        ->middleware('permission:zones.update')
        ->name('zones.members.destroy');

    Route::get('/visitors', [VisitorController::class, 'index'])
        ->middleware('permission:visitors.read')
        ->name('visitors.index');
    Route::get('/visitors/create', [VisitorController::class, 'create'])
        ->middleware('permission:visitors.create')
        ->name('visitors.create');
    Route::post('/visitors', [VisitorController::class, 'store'])
        ->middleware('permission:visitors.create')
        ->name('visitors.store');
    Route::get('/visitors/{visitor}', [VisitorController::class, 'show'])
        ->middleware('permission:visitors.read')
        ->name('visitors.show');
    Route::get('/visitors/{visitor}/edit', [VisitorController::class, 'edit'])
        ->middleware('permission:visitors.update')
        ->name('visitors.edit');
    Route::put('/visitors/{visitor}', [VisitorController::class, 'update'])
        ->middleware('permission:visitors.update')
        ->name('visitors.update');
    Route::delete('/visitors/{visitor}', [VisitorController::class, 'destroy'])
        ->middleware('permission:visitors.delete')
        ->name('visitors.destroy');
    Route::patch('/visitors/{visitor}/status', [VisitorController::class, 'updateStatus'])
        ->middleware('permission:visitors.update')
        ->name('visitors.status.update');
    Route::patch('/visitors/{visitor}/convert', [VisitorController::class, 'convertToMember'])
        ->middleware('permission:visitors.convert')
        ->name('visitors.convert');

    Route::get('/follow-up', [FollowUpController::class, 'index'])
        ->middleware('permission:follow_up.read')
        ->name('follow-up.index');

    Route::get('/leaders', [LeaderController::class, 'index'])
        ->middleware('permission:leaders.read')
        ->name('leaders.index');
    Route::get('/leaders/create', [LeaderController::class, 'create'])
        ->middleware('permission:leaders.create')
        ->name('leaders.create');
    Route::post('/leaders', [LeaderController::class, 'store'])
        ->middleware('permission:leaders.create')
        ->name('leaders.store');
    Route::get('/leaders/{leader}', [LeaderController::class, 'show'])
        ->middleware('permission:leaders.read')
        ->name('leaders.show');
    Route::get('/leaders/{leader}/edit', [LeaderController::class, 'edit'])
        ->middleware('permission:leaders.update')
        ->name('leaders.edit');
    Route::put('/leaders/{leader}', [LeaderController::class, 'update'])
        ->middleware('permission:leaders.update')
        ->name('leaders.update');
    Route::delete('/leaders/{leader}', [LeaderController::class, 'destroy'])
        ->middleware('permission:leaders.delete')
        ->name('leaders.destroy');
    Route::get('/follow-up/pipeline', [FollowUpController::class, 'pipeline'])
        ->middleware('permission:follow_up.read')
        ->name('follow-up.pipeline');
    Route::get('/follow-up/tasks', [FollowUpController::class, 'tasks'])
        ->middleware('permission:follow_up.read')
        ->name('follow-up.tasks');
    Route::post('/follow-up/tasks', [FollowUpController::class, 'storeTask'])
        ->middleware('permission:follow_up.create')
        ->name('follow-up.tasks.store');
    Route::put('/follow-up/tasks/{task}', [FollowUpController::class, 'updateTask'])
        ->middleware('permission:follow_up.update')
        ->name('follow-up.tasks.update');
    Route::delete('/follow-up/tasks/{task}', [FollowUpController::class, 'destroyTask'])
        ->middleware('permission:follow_up.delete')
        ->name('follow-up.tasks.destroy');
    Route::post('/follow-up/history', [FollowUpController::class, 'storeHistory'])
        ->middleware('permission:follow_up.update')
        ->name('follow-up.history.store');

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->middleware('permission:attendance.read')
        ->name('attendance.index');
    Route::get('/attendance/services', [AttendanceController::class, 'services'])
        ->middleware('permission:attendance.read')
        ->name('attendance.services');
    Route::post('/attendance/services', [AttendanceController::class, 'storeService'])
        ->middleware('permission:attendance.create')
        ->name('attendance.services.store');
    Route::delete('/attendance/services/{service}', [AttendanceController::class, 'destroyService'])
        ->middleware('permission:attendance.delete')
        ->name('attendance.services.destroy');
    Route::get('/attendance/services/{service}/edit', [AttendanceController::class, 'editService'])
        ->middleware('permission:attendance.update')
        ->name('attendance.services.edit');
    Route::put('/attendance/services/{service}', [AttendanceController::class, 'updateService'])
        ->middleware('permission:attendance.update')
        ->name('attendance.services.update');
    Route::get('/attendance/services/{service}', [AttendanceController::class, 'showService'])
        ->middleware('permission:attendance.read')
        ->name('attendance.services.show');
    Route::get('/attendance/bulk', [AttendanceController::class, 'bulk'])
        ->middleware('permission:attendance.create')
        ->name('attendance.bulk');
    Route::post('/attendance/bulk', [AttendanceController::class, 'storeBulk'])
        ->middleware('permission:attendance.create')
        ->name('attendance.bulk.store');
    Route::get('/attendance/record', [AttendanceController::class, 'records'])
        ->middleware('permission:attendance.read')
        ->name('attendance.record');
    Route::post('/attendance/record', [AttendanceController::class, 'storeRecord'])
        ->middleware('permission:attendance.create')
        ->name('attendance.record.store');
    Route::get('/attendance/record/{record}/edit', [AttendanceController::class, 'editRecord'])
        ->middleware('permission:attendance.update')
        ->name('attendance.record.edit');
    Route::put('/attendance/record/{record}', [AttendanceController::class, 'updateRecord'])
        ->middleware('permission:attendance.update')
        ->name('attendance.record.update');
    Route::delete('/attendance/record/{record}', [AttendanceController::class, 'destroyRecord'])
        ->middleware('permission:attendance.delete')
        ->name('attendance.record.destroy');
    Route::get('/attendance/reports', [AttendanceController::class, 'reports'])
        ->middleware('permission:attendance.reports.read')
        ->name('attendance.reports');
    Route::get('/attendance/reports/export', [AttendanceController::class, 'exportCsv'])
        ->middleware('permission:attendance.reports.read')
        ->name('attendance.reports.export');
    Route::get('/attendance/members/{member}', [AttendanceController::class, 'memberProfile'])
        ->middleware('permission:attendance.read')
        ->name('attendance.member.profile');
    Route::get('/attendance/scan', [AttendanceController::class, 'scan'])
        ->middleware('permission:attendance.create')
        ->name('attendance.scan');
    Route::post('/attendance/scan/record', [AttendanceController::class, 'processScan'])
        ->middleware('permission:attendance.create')
        ->name('attendance.scan.record');
    Route::post('/attendance/qr/send', [AttendanceController::class, 'sendQr'])
        ->middleware('permission:attendance.create')
        ->name('attendance.qr.send');

    Route::get('/pastoral-care', [PastoralCareController::class, 'index'])
        ->middleware('permission:pastoral_care.read')
        ->name('pastoral-care.index');
    Route::get('/pastoral-care/create', [PastoralCareController::class, 'create'])
        ->middleware('permission:pastoral_care.create')
        ->name('pastoral-care.create');
    Route::post('/pastoral-care', [PastoralCareController::class, 'store'])
        ->middleware('permission:pastoral_care.create')
        ->name('pastoral-care.store');
    Route::get('/pastoral-care/{pastoral_case}', [PastoralCareController::class, 'show'])
        ->middleware('permission:pastoral_care.read')
        ->name('pastoral-care.show');
    Route::put('/pastoral-care/{pastoral_case}', [PastoralCareController::class, 'update'])
        ->middleware('permission:pastoral_care.update')
        ->name('pastoral-care.update');
    Route::delete('/pastoral-care/{pastoral_case}', [PastoralCareController::class, 'destroy'])
        ->middleware('permission:pastoral_care.delete')
        ->name('pastoral-care.destroy');
    Route::post('/pastoral-care/{pastoral_case}/notes', [PastoralCareController::class, 'storeNote'])
        ->middleware('permission:pastoral_care.notes.create')
        ->name('pastoral-care.notes.store');

    Route::get('/prayer-requests', [PrayerRequestController::class, 'index'])
        ->middleware('permission:prayer_requests.read')
        ->name('prayer-requests.index');
    Route::post('/prayer-requests', [PrayerRequestController::class, 'store'])
        ->middleware('permission:prayer_requests.create')
        ->name('prayer-requests.store');
    Route::get('/prayer-requests/{prayer_request}', [PrayerRequestController::class, 'show'])
        ->middleware('permission:prayer_requests.read')
        ->name('prayer-requests.show');
    Route::put('/prayer-requests/{prayer_request}', [PrayerRequestController::class, 'update'])
        ->middleware('permission:prayer_requests.update')
        ->name('prayer-requests.update');
    Route::delete('/prayer-requests/{prayer_request}', [PrayerRequestController::class, 'destroy'])
        ->middleware('permission:prayer_requests.delete')
        ->name('prayer-requests.destroy');

    Route::get('/alerts', [AlertController::class, 'index'])
        ->middleware('permission:alerts.read')
        ->name('alerts.index');
    Route::post('/alerts/run', [AlertController::class, 'run'])
        ->middleware('permission:alerts.run')
        ->name('alerts.run');
    Route::put('/alerts/{alert}', [AlertController::class, 'update'])
        ->middleware('permission:alerts.update')
        ->name('alerts.update');
    Route::delete('/alerts/{alert}', [AlertController::class, 'destroy'])
        ->middleware('permission:alerts.delete')
        ->name('alerts.destroy');

    Route::get('/communications', [CommunicationController::class, 'index'])
        ->middleware('permission:communications.read')
        ->name('communications.index');
    Route::get('/communications/compose', [CommunicationController::class, 'create'])
        ->middleware('permission:communications.create')
        ->name('communications.create');
    Route::post('/communications', [CommunicationController::class, 'store'])
        ->middleware('permission:communications.create')
        ->name('communications.store');
    Route::get('/communications/{communication}', [CommunicationController::class, 'show'])
        ->middleware('permission:communications.read')
        ->name('communications.show');
    Route::put('/communications/{communication}', [CommunicationController::class, 'update'])
        ->middleware('permission:communications.update')
        ->name('communications.update');
    Route::delete('/communications/{communication}', [CommunicationController::class, 'destroy'])
        ->middleware('permission:communications.delete')
        ->name('communications.destroy');
    Route::post('/communications/{communication}/send', [CommunicationController::class, 'send'])
        ->middleware('permission:communications.send')
        ->name('communications.send');
    Route::post('/communications/{communication}/retry', [CommunicationController::class, 'retryFailed'])
        ->middleware('permission:communications.send')
        ->name('communications.retry');

    Route::get('/events', [EventController::class, 'index'])
        ->middleware('permission:events.read')
        ->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])
        ->middleware('permission:events.create')
        ->name('events.create');
    Route::post('/events', [EventController::class, 'store'])
        ->middleware('permission:events.create')
        ->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])
        ->middleware('permission:events.read')
        ->name('events.show');
    Route::put('/events/{event}', [EventController::class, 'update'])
        ->middleware('permission:events.update')
        ->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])
        ->middleware('permission:events.delete')
        ->name('events.destroy');
    Route::post('/events/{event}/registrations', [EventController::class, 'register'])
        ->middleware('permission:events.update')
        ->name('events.registrations.store');
    Route::delete('/events/{event}/registrations/{registration}', [EventController::class, 'destroyRegistration'])
        ->middleware('permission:events.update')
        ->name('events.registrations.destroy');

    Route::get('/volunteers', [VolunteerController::class, 'index'])
        ->middleware('permission:volunteers.read')
        ->name('volunteers.index');
    Route::post('/volunteers/assignments', [VolunteerController::class, 'store'])
        ->middleware('permission:volunteers.create')
        ->name('volunteers.assignments.store');
    Route::put('/volunteers/assignments/{assignment}', [VolunteerController::class, 'update'])
        ->middleware('permission:volunteers.update')
        ->name('volunteers.assignments.update');
    Route::delete('/volunteers/assignments/{assignment}', [VolunteerController::class, 'destroy'])
        ->middleware('permission:volunteers.delete')
        ->name('volunteers.assignments.destroy');

    Route::get('/scorecards', [ScorecardController::class, 'index'])
        ->middleware('permission:scorecards.read')
        ->name('scorecards.index');
    Route::get('/scorecards/zones', [ScorecardController::class, 'zones'])
        ->middleware('permission:scorecards.read')
        ->name('scorecards.zones');
    Route::get('/scorecards/departments', [ScorecardController::class, 'departments'])
        ->middleware('permission:scorecards.read')
        ->name('scorecards.departments');

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:reports.read')
        ->name('reports.index');
    Route::get('/reports/departments', [ReportController::class, 'departments'])
        ->middleware('permission:reports.read')
        ->name('reports.departments');
    Route::get('/reports/departments/export', [ReportController::class, 'departmentsExport'])
        ->middleware('permission:reports.export')
        ->name('reports.departments.export');
    Route::get('/reports/zones', [ReportController::class, 'zones'])
        ->middleware('permission:reports.read')
        ->name('reports.zones');
    Route::get('/reports/zones/export', [ReportController::class, 'zonesExport'])
        ->middleware('permission:reports.export')
        ->name('reports.zones.export');
    Route::get('/reports/events', [ReportController::class, 'events'])
        ->middleware('permission:reports.read')
        ->name('reports.events');
    Route::get('/reports/events/export', [ReportController::class, 'eventsExport'])
        ->middleware('permission:reports.export')
        ->name('reports.events.export');
    Route::get('/reports/volunteers', [ReportController::class, 'volunteers'])
        ->middleware('permission:reports.read')
        ->name('reports.volunteers');
    Route::get('/reports/volunteers/export', [ReportController::class, 'volunteersExport'])
        ->middleware('permission:reports.export')
        ->name('reports.volunteers.export');
    Route::get('/reports/members', [ReportController::class, 'members'])
        ->middleware('permission:reports.read')
        ->name('reports.members');
    Route::get('/reports/members/export', [ReportController::class, 'membersExport'])
        ->middleware('permission:reports.export')
        ->name('reports.members.export');
    Route::get('/reports/finance', [ReportController::class, 'finance'])
        ->middleware('permission:reports.read')
        ->name('reports.finance');
    Route::get('/reports/finance/export', [ReportController::class, 'financeExport'])
        ->middleware('permission:reports.export')
        ->name('reports.finance.export');
    Route::get('/reports/visitors', [ReportController::class, 'visitors'])
        ->middleware('permission:reports.read')
        ->name('reports.visitors');
    Route::get('/reports/visitors/export', [ReportController::class, 'visitorsExport'])
        ->middleware('permission:reports.export')
        ->name('reports.visitors.export');
    Route::get('/reports/pledges', [ReportController::class, 'pledges'])
        ->middleware('permission:reports.read')
        ->name('reports.pledges');
    Route::get('/reports/pledges/export', [ReportController::class, 'pledgesExport'])
        ->middleware('permission:reports.export')
        ->name('reports.pledges.export');
    Route::get('/reports/followup', [ReportController::class, 'followup'])
        ->middleware('permission:reports.read')
        ->name('reports.followup');
    Route::get('/reports/followup/export', [ReportController::class, 'followupExport'])
        ->middleware('permission:reports.export')
        ->name('reports.followup.export');
    Route::get('/reports/pastoral', [ReportController::class, 'pastoral'])
        ->middleware('permission:reports.read')
        ->name('reports.pastoral');
    Route::get('/reports/pastoral/export', [ReportController::class, 'pastoralExport'])
        ->middleware('permission:reports.export')
        ->name('reports.pastoral.export');
    Route::get('/reports/communications', [ReportController::class, 'communications'])
        ->middleware('permission:reports.read')
        ->name('reports.communications');
    Route::get('/reports/communications/export', [ReportController::class, 'communicationsExport'])
        ->middleware('permission:reports.export')
        ->name('reports.communications.export');
    Route::get('/reports/payroll', [ReportController::class, 'payroll'])
        ->middleware('permission:reports.read')
        ->name('reports.payroll');
    Route::get('/reports/payroll/export', [ReportController::class, 'payrollExport'])
        ->middleware('permission:reports.export')
        ->name('reports.payroll.export');

    // Profile (self-service)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Roles & permission matrix
    Route::get('/roles', [RolePermissionController::class, 'index'])
        ->middleware('permission:roles.read')
        ->name('roles.index');
    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'toggle'])
        ->middleware('permission:roles.update')
        ->name('roles.permissions.toggle');

    // Audit log
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('permission:audit_logs.read')
        ->name('audit-logs.index');

    // User restore (soft-deleted)
    Route::post('/users/{user}/restore', [UserManagementController::class, 'restore'])
        ->middleware('permission:users.update')
        ->name('users.restore');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

    // Finance routes
    Route::resource('payroll', App\Http\Controllers\PayrollController::class);
    Route::resource('payroll-categories', App\Http\Controllers\PayrollCategoryController::class)
        ->only(['index', 'store', 'destroy']);
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::resource('expenditures', App\Http\Controllers\ExpenditureController::class)
        ->middleware('permission:expenditures.read');
    Route::resource('department-income', App\Http\Controllers\DepartmentIncomeController::class);
    Route::resource('department-expenses', App\Http\Controllers\DepartmentExpenseController::class);
    Route::resource('income', App\Http\Controllers\IncomeController::class);
    // Income Types — full CRUD inline
    Route::resource('income-types', App\Http\Controllers\IncomeTypeController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('donations', App\Http\Controllers\DonationController::class);
    Route::resource('campaigns', App\Http\Controllers\CampaignController::class);
    Route::resource('pledges', App\Http\Controllers\PledgeController::class);
    Route::resource('missed-pledges', App\Http\Controllers\MissedPledgeController::class);
    Route::resource('pledge-payments', App\Http\Controllers\PledgePaymentController::class);
