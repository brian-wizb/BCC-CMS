<?php

namespace App\Http\Controllers;

use App\Models\DiscipleshipParticipant;
use App\Models\DiscipleshipStageProgress;
use App\Models\Member;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DiscipleshipController extends Controller
{
    private const STAGES = [1, 2, 3, 4];
    private const STATUSES = ['not_started', 'started', 'in_progress', 'completed', 'deferred'];

    private AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $stage = $request->integer('stage');
        $status = $request->string('status')->toString();

        $participants = DiscipleshipParticipant::query()
            ->with(['member:id,full_name,phone', 'stages'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('external_name', 'like', "%{$search}%")
                        ->orWhere('external_phone', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($members) => $members->where('full_name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($stage, self::STAGES, true) && in_array($status, self::STATUSES, true), fn ($query) => $query->whereHas('stages', fn ($stages) => $stages->where('stage_number', $stage)->where('status', $status)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => DiscipleshipParticipant::count(),
            'in_progress' => DiscipleshipStageProgress::where('status', 'in_progress')->distinct('discipleship_participant_id')->count('discipleship_participant_id'),
            'completed' => DiscipleshipParticipant::whereHas('stages', fn ($q) => $q->where('status', 'completed'), '=', 4)->count(),
            'awarded' => DiscipleshipParticipant::whereNotNull('certificate_awarded_at')->count(),
        ];

        return view('discipleship.index', compact('participants', 'search', 'stage', 'status', 'stats'));
    }

    public function create(): View
    {
        $members = Member::query()->orderBy('full_name')->get(['id', 'full_name', 'phone']);

        return view('discipleship.create', compact('members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedParticipant($request);

        if (blank($data['member_id'] ?? null)) {
            $data['member_id'] = null;
        } else {
            $data['external_name'] = $data['external_phone'] = $data['external_email'] = null;
        }

        $participant = DB::transaction(function () use ($data) {
            $participant = DiscipleshipParticipant::create($data);
            foreach (self::STAGES as $stage) {
                $participant->stages()->create(['stage_number' => $stage]);
            }

            return $participant;
        });

        $this->auditLogger->log($request, 'discipleship.participant.create', 'discipleship_participant', $participant->id);

        return redirect()->route('discipleship.show', $participant)->with('status', 'Participant enrolled in discipleship.');
    }

    public function show(DiscipleshipParticipant $participant): View
    {
        $participant->load(['member:id,full_name,phone,email', 'stages' => fn ($query) => $query->orderBy('stage_number')]);

        return view('discipleship.show', ['participant' => $participant, 'statuses' => self::STATUSES]);
    }

    public function updateStage(Request $request, DiscipleshipParticipant $participant, DiscipleshipStageProgress $stage): RedirectResponse
    {
        abort_unless($stage->discipleship_participant_id === $participant->id, 404);
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (in_array($data['status'], ['started', 'in_progress', 'completed'], true) && blank($data['started_at'] ?? null)) {
            $data['started_at'] = $stage->started_at ?: now()->toDateString();
        }
        if ($data['status'] === 'completed' && blank($data['completed_at'] ?? null)) {
            $data['completed_at'] = now()->toDateString();
        }
        if ($data['status'] !== 'completed') {
            $data['completed_at'] = null;
        }

        $stage->update($data);
        $this->auditLogger->log($request, 'discipleship.stage.update', 'discipleship_stage_progress', $stage->id, after: ['status' => $stage->status]);

        return back()->with('status', "Foundation {$stage->stage_number} progress updated.");
    }

    public function awardCertificate(Request $request, DiscipleshipParticipant $participant): RedirectResponse
    {
        $participant->load('stages');
        if (! $participant->has_completed_foundation) {
            throw ValidationException::withMessages(['certificate' => 'Complete Foundations 1–4 before awarding a certificate.']);
        }

        if (! $participant->certificate_awarded_at) {
            $participant->update([
                'certificate_number' => sprintf('DS-%s-%05d', now()->format('Y'), $participant->id),
                'certificate_awarded_at' => now(),
            ]);
            $this->auditLogger->log($request, 'discipleship.certificate.award', 'discipleship_participant', $participant->id);
        }

        return back()->with('status', 'Certificate awarded successfully.');
    }

    public function certificates(): View
    {
        $participants = DiscipleshipParticipant::query()
            ->with('member:id,full_name,phone')
            ->whereNotNull('certificate_awarded_at')
            ->latest('certificate_awarded_at')
            ->paginate(20);

        return view('discipleship.certificates', compact('participants'));
    }

    private function validatedParticipant(Request $request): array
    {
        return $request->validate([
            'member_id' => ['nullable', 'integer', 'exists:members,id', 'unique:discipleship_participants,member_id'],
            'external_name' => ['nullable', 'string', 'max:255', 'required_without:member_id'],
            'external_phone' => ['nullable', 'string', 'max:50'],
            'external_email' => ['nullable', 'email', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
