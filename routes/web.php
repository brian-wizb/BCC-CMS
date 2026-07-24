<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberTimelineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ChildrenMinistryController;
use App\Http\Controllers\DiscipleshipController;
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

    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware('permission:users.read')
        ->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');
    Route::get('/users/{user}/profile-photo', [UserManagementController::class, 'profilePhoto'])
        ->name('users.profile-photo');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('permission:users.update')
        ->name('users.edit');
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
    Route::get('/members/{member}/profile-picture', [MemberController::class, 'profilePicture'])
        ->middleware('permission:members.read')
        ->name('members.profile-picture');
    Route::get('/members/{member}/timeline', [MemberTimelineController::class, 'show'])
        ->middleware('permission:member_timeline.read')
        ->name('members.timeline');
    Route::resource('members', MemberController::class)
        ->middleware('permission:members.read')
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

    // Groups
    Route::get('/groups', [GroupController::class, 'index'])
        ->middleware('permission:groups.read')
        ->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])
        ->middleware('permission:groups.create')
        ->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])
        ->middleware('permission:groups.create')
        ->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])
        ->middleware('permission:groups.read')
        ->name('groups.show');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])
        ->middleware('permission:groups.update')
        ->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])
        ->middleware('permission:groups.update')
        ->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])
        ->middleware('permission:groups.delete')
        ->name('groups.destroy');
    Route::post('/groups/{group}/members', [GroupController::class, 'storeMember'])
        ->middleware('permission:groups.update')
        ->name('groups.members.store');
    Route::post('/groups/{group}/members/bulk', [GroupController::class, 'storeMembersBulk'])
        ->middleware('permission:groups.update')
        ->name('groups.members.bulk');
    Route::delete('/groups/{group}/members/{membership}', [GroupController::class, 'destroyMember'])
        ->middleware('permission:groups.update')
        ->name('groups.members.destroy');

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

    Route::get('/children-ministry', [ChildrenMinistryController::class, 'index'])
        ->middleware('permission:children_ministry.read')
        ->name('children-ministry.index');
    Route::get('/children-ministry/export', [ChildrenMinistryController::class, 'export'])
        ->middleware('permission:children_ministry.export')
        ->name('children-ministry.export');
    Route::get('/children-ministry/create', [ChildrenMinistryController::class, 'create'])
        ->middleware('permission:children_ministry.create')
        ->name('children-ministry.create');
    Route::post('/children-ministry', [ChildrenMinistryController::class, 'store'])
        ->middleware('permission:children_ministry.create')
        ->name('children-ministry.store');
    Route::get('/children-ministry/{childrenMinistry}', [ChildrenMinistryController::class, 'show'])
        ->middleware('permission:children_ministry.read')
        ->name('children-ministry.show');
    Route::get('/children-ministry/{childrenMinistry}/edit', [ChildrenMinistryController::class, 'edit'])
        ->middleware('permission:children_ministry.update')
        ->name('children-ministry.edit');
    Route::put('/children-ministry/{childrenMinistry}', [ChildrenMinistryController::class, 'update'])
        ->middleware('permission:children_ministry.update')
        ->name('children-ministry.update');
    Route::delete('/children-ministry/{childrenMinistry}', [ChildrenMinistryController::class, 'destroy'])
        ->middleware('permission:children_ministry.delete')
        ->name('children-ministry.destroy');

    Route::get('/discipleship', [DiscipleshipController::class, 'index'])
        ->middleware('permission:discipleship.read')
        ->name('discipleship.index');
    Route::get('/discipleship/create', [DiscipleshipController::class, 'create'])
        ->middleware('permission:discipleship.create')
        ->name('discipleship.create');
    Route::post('/discipleship', [DiscipleshipController::class, 'store'])
        ->middleware('permission:discipleship.create')
        ->name('discipleship.store');
    Route::get('/discipleship/certificates', [DiscipleshipController::class, 'certificates'])
        ->middleware('permission:discipleship.read')
        ->name('discipleship.certificates');
    Route::get('/discipleship/{participant}', [DiscipleshipController::class, 'show'])
        ->middleware('permission:discipleship.read')
        ->name('discipleship.show');
    Route::put('/discipleship/{participant}/stages/{stage}', [DiscipleshipController::class, 'updateStage'])
        ->middleware('permission:discipleship.update')
        ->name('discipleship.stages.update');
    Route::post('/discipleship/{participant}/certificate', [DiscipleshipController::class, 'awardCertificate'])
        ->middleware('permission:discipleship.award')
        ->name('discipleship.certificate.award');

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
    Route::get('/follow-up/people', [FollowUpController::class, 'peopleByType'])
        ->middleware('permission:follow_up.read')
        ->name('follow-up.people');
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
    Route::get('/attendance/scan/search-people', [AttendanceController::class, 'searchScanPeople'])
        ->middleware('permission:attendance.create')
        ->name('attendance.scan.search-people');
    Route::post('/attendance/scan/record-person', [AttendanceController::class, 'recordScanPerson'])
        ->middleware('permission:attendance.create')
        ->name('attendance.scan.record-person');
    Route::post('/attendance/qr/send', [AttendanceController::class, 'sendQr'])
        ->middleware('permission:attendance.create')
        ->name('attendance.qr.send');

    Route::get('/alerts', [AlertController::class, 'index'])
        ->middleware('permission:alerts.read')
        ->name('alerts.index');
    Route::put('/alerts/{alert}', [AlertController::class, 'update'])
        ->middleware('permission:alerts.update')
        ->name('alerts.update');
    Route::post('/alerts/{alert}/assign-follow-up-task', [AlertController::class, 'assignFollowUpTask'])
        ->middleware('permission:alerts.update')
        ->name('alerts.assign-follow-up-task');
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
    Route::get('/operations/communications-credits', [CommunicationController::class, 'operations'])
        ->middleware('permission:settings.manage')
        ->name('communications.operations');
    Route::post('/operations/communications-credits', [CommunicationController::class, 'updateOperations'])
        ->middleware('permission:settings.manage')
        ->name('communications.operations.update');

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
    Route::get('/reports/groups', [ReportController::class, 'groups'])
        ->middleware('permission:reports.read')
        ->name('reports.groups');
    Route::get('/reports/groups/export', [ReportController::class, 'groupsExport'])
        ->middleware('permission:reports.export')
        ->name('reports.groups.export');
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
    Route::get('/reports/children-ministry', [ReportController::class, 'childrenMinistry'])
        ->middleware('permission:reports.read')
        ->name('reports.children-ministry');
    Route::get('/reports/children-ministry/export', [ReportController::class, 'childrenMinistryExport'])
        ->middleware('permission:reports.export')
        ->name('reports.children-ministry.export');
    Route::get('/reports/discipleship', [ReportController::class, 'discipleship'])
        ->middleware('permission:reports.read')
        ->name('reports.discipleship');
    Route::get('/reports/discipleship/export', [ReportController::class, 'discipleshipExport'])
        ->middleware('permission:reports.export')
        ->name('reports.discipleship.export');
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
    Route::get('/reports/communications', [ReportController::class, 'communications'])
        ->middleware('permission:reports.read')
        ->name('reports.communications');
    Route::get('/reports/communications/export', [ReportController::class, 'communicationsExport'])
        ->middleware('permission:reports.export')
        ->name('reports.communications.export');
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

Route::middleware(['auth', 'active'])->group(function () {
    // Finance routes
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::get('expenditures/export', [App\Http\Controllers\ExpenditureController::class, 'export'])
        ->name('expenditures.export');
    Route::resource('expenditures', App\Http\Controllers\ExpenditureController::class)
        ->middleware('permission:expenditures.read');
    Route::get('department-income/export', [App\Http\Controllers\DepartmentIncomeController::class, 'export'])
        ->name('department-income.export');
    Route::resource('department-income', App\Http\Controllers\DepartmentIncomeController::class);
    Route::get('department-expenses/export', [App\Http\Controllers\DepartmentExpenseController::class, 'export'])
        ->name('department-expenses.export');
    Route::resource('department-expenses', App\Http\Controllers\DepartmentExpenseController::class);
    Route::get('income/export', [App\Http\Controllers\IncomeController::class, 'export'])
        ->middleware('permission:income.read')
        ->name('income.export');
    Route::get('income', [App\Http\Controllers\IncomeController::class, 'index'])
        ->middleware('permission:income.read')->name('income.index');
    Route::get('income/create', [App\Http\Controllers\IncomeController::class, 'create'])
        ->middleware('permission:income.create')->name('income.create');
    Route::post('income', [App\Http\Controllers\IncomeController::class, 'store'])
        ->middleware('permission:income.create')->name('income.store');
    Route::get('income/{income}/edit', [App\Http\Controllers\IncomeController::class, 'edit'])
        ->middleware('permission:income.update')->name('income.edit');
    Route::put('income/{income}', [App\Http\Controllers\IncomeController::class, 'update'])
        ->middleware('permission:income.update')->name('income.update');
    Route::delete('income/{income}', [App\Http\Controllers\IncomeController::class, 'destroy'])
        ->middleware('permission:income.delete')->name('income.destroy');
    // Income Types — full CRUD inline
    Route::get('income-types/export', [App\Http\Controllers\IncomeTypeController::class, 'export'])
        ->middleware('permission:income.read')
        ->name('income-types.export');
    Route::get('income-types', [App\Http\Controllers\IncomeTypeController::class, 'index'])
        ->middleware('permission:income.read')->name('income-types.index');
    Route::post('income-types', [App\Http\Controllers\IncomeTypeController::class, 'store'])
        ->middleware('permission:income.create')->name('income-types.store');
    Route::put('income-types/{income_type}', [App\Http\Controllers\IncomeTypeController::class, 'update'])
        ->middleware('permission:income.update')->name('income-types.update');
    Route::delete('income-types/{income_type}', [App\Http\Controllers\IncomeTypeController::class, 'destroy'])
        ->middleware('permission:income.delete')->name('income-types.destroy');
    Route::get('givings/export', [App\Http\Controllers\DonationController::class, 'export'])
        ->middleware('permission:givings.export')
        ->name('givings.export');
    Route::get('givings', [App\Http\Controllers\DonationController::class, 'index'])
        ->middleware('permission:givings.read')->name('givings.index');
    Route::get('givings/create', [App\Http\Controllers\DonationController::class, 'create'])
        ->middleware('permission:givings.create')->name('givings.create');
    Route::post('givings', [App\Http\Controllers\DonationController::class, 'store'])
        ->middleware('permission:givings.create')->name('givings.store');
    Route::get('givings/{donation}/edit', [App\Http\Controllers\DonationController::class, 'edit'])
        ->middleware('permission:givings.update')->name('givings.edit');
    Route::put('givings/{donation}', [App\Http\Controllers\DonationController::class, 'update'])
        ->middleware('permission:givings.update')->name('givings.update');
    Route::delete('givings/{donation}', [App\Http\Controllers\DonationController::class, 'destroy'])
        ->middleware('permission:givings.delete')->name('givings.destroy');
    Route::get('campaigns/export', [App\Http\Controllers\CampaignController::class, 'export'])
        ->name('campaigns.export');
    Route::resource('campaigns', App\Http\Controllers\CampaignController::class);
    Route::get('pledges/export', [App\Http\Controllers\PledgeController::class, 'export'])
        ->name('pledges.export');
    Route::resource('pledges', App\Http\Controllers\PledgeController::class);
    Route::get('missed-pledges/export', [App\Http\Controllers\MissedPledgeController::class, 'export'])
        ->name('missed-pledges.export');
    Route::resource('missed-pledges', App\Http\Controllers\MissedPledgeController::class);
    Route::get('pledge-payments/export', [App\Http\Controllers\PledgePaymentController::class, 'export'])
        ->name('pledge-payments.export');
    Route::resource('pledge-payments', App\Http\Controllers\PledgePaymentController::class);
});
