<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Family;
use App\Models\FollowUpTask;
use App\Models\Leader;
use App\Models\Member;
use App\Models\Service;
use App\Models\Visitor;
use App\Models\Zone;
use App\Services\AuditLogger;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    // ── Dashboard ─────────────────────────────────────────────────────────

    public function index(): View
    {
        $lastService = Service::query()->orderByDesc('service_date')->first();

        $thisMonthPresent = AttendanceRecord::query()
            ->where('attendance_status', 'present')
            ->whereMonth('recorded_at', now()->month)
            ->whereYear('recorded_at', now()->year)
            ->count();

        $totalServices = Service::query()->count();
        $totalRecords  = AttendanceRecord::query()->count();

        return view('attendance.index', compact(
            'lastService', 'thisMonthPresent', 'totalServices', 'totalRecords'
        ));
    }

    // ── Services ──────────────────────────────────────────────────────────

    public function services(): View
    {
        $services = Service::query()
            ->withCount('attendanceRecords')
            ->orderByDesc('service_date')
            ->paginate(15);

        return view('attendance.services', [
            'services'       => $services,
            'serviceTypes'   => config('attendance.service_types'),
            'modes'          => config('attendance.modes'),
            'recurrenceRules'=> config('attendance.recurrence_rules'),
        ]);
    }

    public function storeService(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'service_type'     => ['required', 'string', Rule::in(array_keys(config('attendance.service_types')))],
            'service_date'     => ['required', 'date'],
            'start_time'       => ['nullable', 'date_format:H:i'],
            'end_time'         => ['nullable', 'date_format:H:i'],
            'location'         => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'recurrence_rule'  => ['nullable', 'string', Rule::in(array_keys(config('attendance.recurrence_rules')))],
            'attendance_mode'  => ['required', 'string', Rule::in(array_keys(config('attendance.modes')))],
        ]);

        $service = Service::query()->create($data);

        $this->auditLogger->log($request, 'service.create', 'service', $service->id,
            null, ['name' => $service->name, 'service_date' => $service->service_date]);

        return back()->with('status', 'Service created successfully.');
    }

    public function editService(Service $service): View
    {
        return view('attendance.service-edit', [
            'service'        => $service,
            'serviceTypes'   => config('attendance.service_types'),
            'modes'          => config('attendance.modes'),
            'recurrenceRules'=> config('attendance.recurrence_rules'),
        ]);
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'service_type'     => ['required', 'string', Rule::in(array_keys(config('attendance.service_types')))],
            'service_date'     => ['required', 'date'],
            'start_time'       => ['nullable', 'date_format:H:i'],
            'end_time'         => ['nullable', 'date_format:H:i'],
            'location'         => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'recurrence_rule'  => ['nullable', 'string', Rule::in(array_keys(config('attendance.recurrence_rules')))],
            'attendance_mode'  => ['required', 'string', Rule::in(array_keys(config('attendance.modes')))],
        ]);

        $service->update($data);

        return redirect()->route('attendance.services')
            ->with('status', 'Service updated successfully.');
    }

    public function destroyService(Service $service): RedirectResponse
    {
        $count = $service->attendanceRecords()->count();
        $service->delete(); // cascades

        return back()->with('status', "Service deleted along with {$count} attendance records.");
    }

    public function showService(Service $service): View
    {
        $service->loadCount([
            'attendanceRecords as present_count' => fn ($q) => $q->where('attendance_status', 'present'),
            'attendanceRecords as absent_count'  => fn ($q) => $q->where('attendance_status', 'absent'),
            'attendanceRecords as excused_count' => fn ($q) => $q->where('attendance_status', 'excused'),
            'attendanceRecords as late_count'    => fn ($q) => $q->where('attendance_status', 'late'),
        ]);

        $records = AttendanceRecord::query()
            ->with(['member', 'visitor', 'family', 'zone', 'recorder'])
            ->where('service_id', $service->id)
            ->latest('recorded_at')
            ->paginate(30);

        // Zone breakdown
        $byZone = AttendanceRecord::query()
            ->selectRaw('zone_id, attendance_status, COUNT(*) as total')
            ->where('service_id', $service->id)
            ->whereNotNull('zone_id')
            ->groupBy('zone_id', 'attendance_status')
            ->with('zone')
            ->get()
            ->groupBy('zone_id');

        $qrUrl = URL::signedRoute('attendance.checkin', ['service' => $service->id]);

        return view('attendance.service-show', compact('service', 'records', 'byZone', 'qrUrl'));
    }

    // ── Bulk Attendance Sheet ─────────────────────────────────────────────

    public function bulk(Request $request): View
    {
        $serviceId = $request->integer('service_id');
        $service   = $serviceId ? Service::find($serviceId) : null;

        $members  = $service
            ? Member::query()->orderBy('zone')->orderBy('full_name')
                ->get(['id', 'full_name', 'zone', 'gender'])
            : collect();

        // If service selected, fetch existing records keyed by member_id
        $existing = $service
            ? AttendanceRecord::query()
                ->where('service_id', $serviceId)
                ->whereNotNull('member_id')
                ->pluck('attendance_status', 'member_id')
            : collect();

        return view('attendance.bulk', [
            'services' => Service::query()->orderByDesc('service_date')->get(['id', 'name', 'service_date']),
            'service'  => $service,
            'members'  => $members,
            'existing' => $existing,
            'statuses' => config('attendance.statuses'),
            'modes'    => config('attendance.modes'),
            'zones'    => Zone::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeBulk(Request $request): RedirectResponse
    {
        $request->validate([
            'service_id'      => ['required', 'integer', Rule::exists('services', 'id')],
            'attendance_mode' => ['required', 'string', Rule::in(array_keys(config('attendance.modes')))],
            'records'         => ['required', 'array'],
            'records.*.member_id'        => ['required', 'integer', Rule::exists('members', 'id')],
            'records.*.attendance_status'=> ['required', 'string', Rule::in(array_keys(config('attendance.statuses')))],
            'records.*.zone_id'          => ['nullable', 'integer', Rule::exists('zones', 'id')],
            'records.*.check_in_time'    => ['nullable', 'date_format:H:i'],
            'records.*.notes'            => ['nullable', 'string', 'max:255'],
        ]);

        $serviceId = (int) $request->input('service_id');
        $mode      = $request->input('attendance_mode');
        $now       = now();
        $userId    = auth()->id();
        $threshold = config('attendance.absence_alert_threshold', 3);

        $upserted  = 0;
        $absences  = [];

        DB::transaction(function () use ($request, $serviceId, $mode, $now, $userId, $threshold, &$upserted, &$absences) {
            foreach ((array) $request->input('records') as $row) {
                $memberId = (int) $row['member_id'];
                $status   = $row['attendance_status'];

                AttendanceRecord::query()->updateOrCreate(
                    ['service_id' => $serviceId, 'member_id' => $memberId],
                    [
                        'attendance_status' => $status,
                        'attendance_mode'   => $mode,
                        'zone_id'           => $row['zone_id'] ?? null,
                        'check_in_time'     => isset($row['check_in_time']) && $row['check_in_time']
                            ? $now->toDateString().' '.$row['check_in_time']
                            : null,
                        'notes'       => $row['notes'] ?? null,
                        'recorded_by' => $userId,
                        'recorded_at' => $now,
                    ]
                );
                $upserted++;

                // Track absences for auto-alert
                if ($status === 'absent') {
                    $absences[] = $memberId;
                }
            }

            // Auto follow-up: consecutive absence check
            foreach ($absences as $memberId) {
                $consecutiveAbsences = AttendanceRecord::query()
                    ->where('member_id', $memberId)
                    ->where('attendance_status', 'absent')
                    ->latest('recorded_at')
                    ->limit($threshold)
                    ->count();

                if ($consecutiveAbsences >= $threshold) {
                    $exists = FollowUpTask::query()
                        ->where('person_type', 'member')
                        ->where('person_id', $memberId)
                        ->where('status', '!=', 'completed')
                        ->where('task_type', 'visit')
                        ->exists();

                    if (! $exists) {
                        FollowUpTask::query()->create([
                            'person_type' => 'member',
                            'person_id'   => $memberId,
                            'task_type'   => 'visit',
                            'priority'    => 'high',
                            'status'      => 'pending',
                            'notes'       => "Auto-generated: {$threshold} consecutive absences recorded.",
                            'due_date'    => now()->addDays(7),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('attendance.bulk', ['service_id' => $serviceId])
            ->with('status', "Attendance saved for {$upserted} members.");
    }

    // ── Single Record (manual entry) ──────────────────────────────────────

    public function records(Request $request): View
    {
        $serviceId = $request->integer('service_id');
        $status    = trim((string) $request->string('status'));

        $records = AttendanceRecord::query()
            ->with(['service', 'member', 'visitor', 'family', 'zone', 'recorder'])
            ->when($serviceId, fn ($q) => $q->where('service_id', $serviceId))
            ->when($status,    fn ($q) => $q->where('attendance_status', $status))
            ->latest('recorded_at')
            ->paginate(20)
            ->withQueryString();

        return view('attendance.record', [
            'records'   => $records,
            'serviceId' => $serviceId,
            'status'    => $status,
            'services'  => Service::query()->orderByDesc('service_date')->get(['id', 'name', 'service_date']),
            'members'   => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
            'visitors'  => Visitor::query()->orderBy('full_name')->get(['id', 'full_name']),
            'families'  => Family::query()->orderBy('head_of_family')->get(['id', 'head_of_family']),
            'zones'     => Zone::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'statuses'  => config('attendance.statuses'),
            'modes'     => config('attendance.modes'),
        ]);
    }

    public function storeRecord(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_id'        => ['required', 'integer', Rule::exists('services', 'id')],
            'member_id'         => ['nullable', 'integer', Rule::exists('members', 'id')],
            'visitor_id'        => ['nullable', 'integer', Rule::exists('visitors', 'id')],
            'family_id'         => ['nullable', 'integer', Rule::exists('families', 'id')],
            'department_id'     => ['nullable', 'integer', Rule::exists('departments', 'id')],
            'zone_id'           => ['nullable', 'integer', Rule::exists('zones', 'id')],
            'attendance_status' => ['required', 'string', Rule::in(array_keys(config('attendance.statuses')))],
            'attendance_mode'   => ['required', 'string', Rule::in(array_keys(config('attendance.modes')))],
            'check_in_time'     => ['nullable', 'date_format:H:i'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        // Ensure at least one person is selected
        if (! $data['member_id'] && ! $data['visitor_id'] && ! $data['family_id']) {
            return back()->withErrors(['member_id' => 'Select at least one person (member, visitor, or family).'])->withInput();
        }

        $data['recorded_by'] = auth()->id();
        $data['recorded_at'] = now();

        // Build unique key for upsert
        $uniqueKey = array_filter([
            'service_id'  => $data['service_id'],
            'member_id'   => $data['member_id']  ?? null,
            'visitor_id'  => $data['visitor_id'] ?? null,
            'family_id'   => $data['family_id']  ?? null,
        ]);

        AttendanceRecord::query()->updateOrCreate($uniqueKey, $data);

        // Auto follow-up for first-time visitor
        if (! empty($data['visitor_id'])) {
            $visitorCount = AttendanceRecord::query()
                ->where('visitor_id', $data['visitor_id'])->count();

            if ($visitorCount === 1) {
                FollowUpTask::query()->firstOrCreate(
                    ['person_type' => 'visitor', 'person_id' => $data['visitor_id'], 'task_type' => 'call'],
                    [
                        'priority' => 'high',
                        'status'   => 'pending',
                        'notes'    => 'Auto-generated: first visit recorded.',
                        'due_date' => now()->addDays(3),
                    ]
                );
            }
        }

        return back()->with('status', 'Attendance recorded successfully.');
    }

    public function editRecord(AttendanceRecord $record): View
    {
        return view('attendance.record-edit', [
            'record'   => $record->load(['service', 'member', 'visitor', 'family', 'zone']),
            'zones'    => Zone::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'statuses' => config('attendance.statuses'),
            'modes'    => config('attendance.modes'),
        ]);
    }

    public function updateRecord(Request $request, AttendanceRecord $record): RedirectResponse
    {
        $data = $request->validate([
            'attendance_status' => ['required', 'string', Rule::in(array_keys(config('attendance.statuses')))],
            'attendance_mode'   => ['required', 'string', Rule::in(array_keys(config('attendance.modes')))],
            'zone_id'           => ['nullable', 'integer', Rule::exists('zones', 'id')],
            'check_in_time'     => ['nullable', 'date_format:H:i'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        $record->update($data);

        return redirect()->route('attendance.record', ['service_id' => $record->service_id])
            ->with('status', 'Record updated.');
    }

    public function destroyRecord(AttendanceRecord $record): RedirectResponse
    {
        $serviceId = $record->service_id;
        $record->delete();

        return redirect()->route('attendance.record', ['service_id' => $serviceId])
            ->with('status', 'Attendance record deleted.');
    }

    // ── Reports ───────────────────────────────────────────────────────────

    public function reports(Request $request): View
    {
        $from = $request->date('from') ?? now()->startOfYear();
        $to   = $request->date('to')   ?? now();

        $statusCounts = AttendanceRecord::query()
            ->selectRaw('attendance_status, COUNT(*) as total')
            ->whereBetween('recorded_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('attendance_status')
            ->pluck('total', 'attendance_status');

        $byService = Service::query()
            ->withCount([
                'attendanceRecords as present_count' => fn ($q) => $q->where('attendance_status', 'present')
                    ->whereBetween('recorded_at', [$from->startOfDay(), $to->endOfDay()]),
                'attendanceRecords as absent_count'  => fn ($q) => $q->where('attendance_status', 'absent')
                    ->whereBetween('recorded_at', [$from->startOfDay(), $to->endOfDay()]),
                'attendanceRecords as excused_count' => fn ($q) => $q->where('attendance_status', 'excused')
                    ->whereBetween('recorded_at', [$from->startOfDay(), $to->endOfDay()]),
                'attendanceRecords as late_count'    => fn ($q) => $q->where('attendance_status', 'late')
                    ->whereBetween('recorded_at', [$from->startOfDay(), $to->endOfDay()]),
            ])
            ->whereBetween('service_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('service_date')
            ->paginate(20)
            ->withQueryString();

        // Trend: monthly present counts for chart (last 12 months)
        $trend = AttendanceRecord::query()
            ->selectRaw("DATE_FORMAT(recorded_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('attendance_status', 'present')
            ->where('recorded_at', '>=', now()->subMonths(12)->startOfMonth())
            ->groupByRaw("DATE_FORMAT(recorded_at, '%Y-%m')")
            ->orderBy('month')
            ->get();

        // Top attending members
        $topMembers = DB::table('attendance_records')
            ->join('members', 'members.id', '=', 'attendance_records.member_id')
            ->selectRaw('members.id, members.full_name, COUNT(*) as times_present')
            ->where('attendance_records.attendance_status', 'present')
            ->whereBetween('attendance_records.recorded_at', [$from, $to])
            ->groupBy('members.id', 'members.full_name')
            ->orderByDesc('times_present')
            ->limit(10)
            ->get();

        return view('attendance.reports', [
            'presentCount' => $statusCounts->get('present', 0),
            'absentCount'  => $statusCounts->get('absent', 0),
            'excusedCount' => $statusCounts->get('excused', 0),
            'lateCount'    => $statusCounts->get('late', 0),
            'byService'    => $byService,
            'trend'        => $trend,
            'topMembers'   => $topMembers,
            'from'         => $from,
            'to'           => $to,
        ]);
    }

    public function exportCsv(Request $request): Response
    {
        $serviceId = $request->integer('service_id');
        $from      = $request->date('from') ?? now()->startOfYear();
        $to        = $request->date('to')   ?? now();

        $records = AttendanceRecord::query()
            ->with(['service', 'member', 'visitor', 'family', 'zone', 'recorder'])
            ->when($serviceId, fn ($q) => $q->where('service_id', $serviceId))
            ->whereBetween('recorded_at', [$from->startOfDay(), $to->endOfDay()])
            ->latest('recorded_at')
            ->get();

        $csv = implode(',', ['Service', 'Date', 'Person', 'Type', 'Status', 'Mode', 'Zone', 'Check-In Time', 'Notes', 'Recorded By', 'Recorded At'])."\n";

        foreach ($records as $r) {
            $csv .= implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', (string) $v).'"', [
                $r->service?->name ?? '',
                $r->service?->service_date?->format('d M Y') ?? '',
                $r->person_name,
                $r->person_type,
                $r->attendance_status,
                $r->attendance_mode,
                $r->zone?->name ?? '',
                $r->check_in_time?->format('H:i') ?? '',
                $r->notes ?? '',
                $r->recorder?->full_name ?? '',
                $r->recorded_at?->format('d M Y H:i') ?? '',
            ]))."\n";
        }

        $filename = 'attendance-export-'.now()->format('Y-m-d').'.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── Member Profile ────────────────────────────────────────────────────

    public function memberProfile(Member $member): View
    {
        $records = AttendanceRecord::query()
            ->with(['service'])
            ->where('member_id', $member->id)
            ->latest('recorded_at')
            ->paginate(20);

        $totalServices = Service::query()->count();
        $attended      = AttendanceRecord::query()
            ->where('member_id', $member->id)
            ->whereIn('attendance_status', ['present', 'late'])
            ->count();
        $rate = $totalServices > 0 ? round(($attended / $totalServices) * 100) : 0;

        // Streak: consecutive present/late from most recent
        $recentStatuses = AttendanceRecord::query()
            ->where('member_id', $member->id)
            ->orderByDesc('recorded_at')
            ->pluck('attendance_status');

        $streak = 0;
        foreach ($recentStatuses as $s) {
            if ($s === 'present' || $s === 'late') {
                $streak++;
            } else {
                break;
            }
        }

        // Monthly trend for last 12 months
        $trendData = DB::table('attendance_records as ar')
            ->join('services as s', 's.id', '=', 'ar.service_id')
            ->selectRaw("DATE_FORMAT(s.service_date, '%Y-%m') as month,
                         SUM(CASE WHEN ar.attendance_status IN ('present','late') THEN 1 ELSE 0 END) as attended,
                         COUNT(*) as total_recorded")
            ->where('ar.member_id', $member->id)
            ->where('s.service_date', '>=', now()->subMonths(12)->startOfMonth()->toDateString())
            ->groupByRaw("DATE_FORMAT(s.service_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        return view('attendance.member-profile', compact(
            'member', 'records', 'rate', 'streak', 'attended', 'totalServices', 'trendData'
        ));
    }

    // ── Usher QR Scan Page ────────────────────────────────────────────────

    public function scan(): View
    {
        return view('attendance.scan', [
            'services' => Service::query()->orderByDesc('service_date')->limit(20)->get(['id', 'name', 'service_date', 'start_time']),
        ]);
    }

    public function processScan(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'token'      => ['required', 'string', 'max:64'],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')],
        ]);

        $token     = $data['token'];
        $serviceId = (int) $data['service_id'];

        // Find person across members, visitors, leaders
        $field  = null;
        $person = null;

        if ($m = Member::query()->where('qr_token', $token)->first()) {
            $person = $m;
            $field  = 'member_id';
        } elseif ($v = Visitor::query()->where('qr_token', $token)->first()) {
            $person = $v;
            $field  = 'visitor_id';
        } elseif ($l = Leader::query()->where('qr_token', $token)->first()) {
            // Leaders linked to a member record are tracked as members
            if ($l->member_id) {
                $person = Member::find($l->member_id);
                $field  = 'member_id';
            } else {
                return response()->json(['error' => 'Leader is not linked to a member record.'], 422);
            }
        }

        if (! $person || ! $field) {
            return response()->json(['error' => 'QR code not recognised. Please contact an administrator.'], 404);
        }

        // Auto-detect late status
        $service   = Service::find($serviceId);
        $graceMins = config('attendance.late_grace_minutes', 15);
        $isLate    = false;
        if ($service?->start_time) {
            $isLate = now()->isAfter(
                now()->setTimeFromTimeString($service->start_time)->addMinutes($graceMins)
            );
        }

        AttendanceRecord::query()->updateOrCreate(
            ['service_id' => $serviceId, $field => $person->id],
            [
                'attendance_status' => $isLate ? 'late' : 'present',
                'attendance_mode'   => 'in_person',
                'check_in_time'     => now(),
                'recorded_by'       => auth()->id(),
                'recorded_at'       => now(),
            ]
        );

        // Auto follow-up for first-time visitor
        if ($field === 'visitor_id') {
            $count = AttendanceRecord::query()->where('visitor_id', $person->id)->count();
            if ($count === 1) {
                FollowUpTask::query()->firstOrCreate(
                    ['person_type' => 'visitor', 'person_id' => $person->id, 'task_type' => 'call'],
                    ['priority' => 'high', 'status' => 'pending', 'notes' => 'Auto-generated: first visit via QR scan.', 'due_date' => now()->addDays(3)]
                );
            }
        }

        return response()->json([
            'success' => true,
            'name'    => $person->full_name ?? ($person->head_of_family ?? 'Unknown'),
            'type'    => str_replace('_id', '', $field),
            'status'  => $isLate ? 'late' : 'present',
            'time'    => now()->format('H:i'),
        ]);
    }

    // ── WhatsApp QR Sending ────────────────────────────────────────────────

    public function sendQr(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'person_type' => ['required', 'string', Rule::in(['member', 'visitor', 'leader'])],
            'person_id'   => ['required', 'integer'],
        ]);

        $person = match ($data['person_type']) {
            'member'  => Member::findOrFail($data['person_id']),
            'visitor' => Visitor::findOrFail($data['person_id']),
            'leader'  => Leader::findOrFail($data['person_id']),
        };

        if (empty($person->phone)) {
            return response()->json(['error' => 'This person has no phone number on record.'], 422);
        }

        if (empty($person->qr_token)) {
            return response()->json(['error' => 'No QR token assigned. Please contact an administrator.'], 422);
        }

        try {
            $phone = WhatsAppService::normalisePhone($person->phone);
            $sid   = (new WhatsAppService())->sendQrCode($phone, $person->full_name, $person->qr_token);

            return response()->json([
                'success' => true,
                'message' => "QR code sent to {$phone}",
                'sid'     => $sid,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'WhatsApp send failed: ' . $e->getMessage()], 500);
        }
    }

    // ── QR / Self Check-In ────────────────────────────────────────────────

    public function checkin(Request $request): View|RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This check-in link has expired or is invalid.');
        }

        $service = Service::findOrFail($request->integer('service'));

        return view('attendance.checkin', compact('service'));
    }

    public function storeCheckin(Request $request): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This check-in link has expired.');
        }

        $data = $request->validate([
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'member_id'  => ['required', 'integer', Rule::exists('members', 'id')],
        ]);

        $service    = Service::find($data['service_id']);
        $startTime  = $service?->start_time;
        $graceMins  = config('attendance.late_grace_minutes', 15);
        $isLate     = false;

        if ($startTime) {
            $serviceStart = now()->setTimeFromTimeString($startTime);
            $isLate       = now()->isAfter($serviceStart->addMinutes($graceMins));
        }

        AttendanceRecord::query()->updateOrCreate(
            ['service_id' => $data['service_id'], 'member_id' => $data['member_id']],
            [
                'attendance_status' => $isLate ? 'late' : 'present',
                'attendance_mode'   => 'in_person',
                'check_in_time'     => now(),
                'recorded_by'       => auth()->id() ?? $data['member_id'],
                'recorded_at'       => now(),
            ]
        );

        $statusMsg = $isLate ? 'Checked in as Late.' : 'Checked in successfully!';

        return back()->with('status', $statusMsg);
    }
}
