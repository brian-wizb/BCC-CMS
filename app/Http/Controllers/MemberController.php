<?php

namespace App\Http\Controllers;

use App\Http\Requests\Members\StoreMemberRequest;
use App\Http\Requests\Members\UpdateMemberRequest;
use App\Models\Family;
use App\Models\Member;
use App\Models\University;
use App\Models\Zone;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $dateFrom = $request->filled('date_from') ? $request->string('date_from')->toString() : null;
        $dateTo = $request->filled('date_to') ? $request->string('date_to')->toString() : null;
        $maritalStatus = trim((string) $request->string('marital_status'));
        $employmentStatus = trim((string) $request->string('employment_status'));
        $universityStudent = trim((string) $request->string('university_student'));

        $members = Member::query()
            ->with('university:id,name')
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
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('zone', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('members.index', [
            'members' => $members,
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'maritalStatus' => $maritalStatus,
            'employmentStatus' => $employmentStatus,
            'universityStudent' => $universityStudent,
        ]);
    }

    public function create(): View
    {
        $families = Family::query()->orderBy('head_of_family')->get(['id', 'head_of_family']);
        $partners = Member::query()->orderBy('full_name')->get(['id', 'full_name', 'tithe_code']);
        $universities = University::query()->orderBy('type')->orderBy('name')->get(['id', 'name', 'type', 'country']);
        $zones = Zone::query()->orderBy('name')->pluck('name')->all();
        $nextTitheCode = Member::nextTitheCode();

        return view('members.create', [
            'member' => new Member([
                'is_born_again' => false,
                'is_baptized' => false,
                'holy_spirit_baptised' => false,
                'share_partner_tithe_code' => false,
                'is_university_student' => false,
                'tithe_code' => $nextTitheCode,
            ]),
            'families' => $families,
            'partners' => $partners,
            'universities' => $universities,
            'zones' => $zones,
            'nextTitheCode' => $nextTitheCode,
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $member = DB::transaction(function () use ($data) {
            $member = Member::query()->create($this->normalizedMemberData($data, null));
            $this->syncPartnerMarriageDetails($member, $data);

            return $member->fresh('partnerMember');
        });

        $this->auditLogger->log(
            request: $request,
            action: 'member.create',
            entityType: 'member',
            entityId: $member->id,
            after: ['full_name' => $member->full_name, 'gender' => $member->gender],
        );

        return redirect()->route('members.index')->with('status', 'Member added successfully.');
    }

    public function show(Member $member): View
    {
        $member->loadMissing('partnerMember', 'university');

        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $families = Family::query()->orderBy('head_of_family')->get(['id', 'head_of_family']);
        $partners = Member::query()
            ->whereKeyNot($member->id)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'tithe_code']);
        $universities = University::query()->orderBy('type')->orderBy('name')->get(['id', 'name', 'type', 'country']);
        $zones = Zone::query()->orderBy('name')->pluck('name')->all();

        return view('members.edit', compact('member', 'families', 'partners', 'universities', 'zones'));
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $data = $request->validated();
        $before = Arr::only($member->toArray(), ['full_name', 'gender', 'phone', 'zone', 'residency']);
        $member = DB::transaction(function () use ($member, $data) {
            $originalPartnerId = $member->partner_member_id;
            $member->update($this->normalizedMemberData($data, $member));
            $this->syncPartnerMarriageDetails($member, $data, $originalPartnerId);

            return $member->fresh('partnerMember');
        });

        $this->auditLogger->log(
            request: $request,
            action: 'member.update',
            entityType: 'member',
            entityId: $member->id,
            before: $before,
            after: Arr::only($member->fresh()->toArray(), ['full_name', 'gender', 'phone', 'zone', 'residency']),
        );

        return redirect()->route('members.show', $member)->with('status', 'Member updated successfully.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $before = Arr::only($member->toArray(), ['full_name', 'gender', 'phone']);
        $memberId = $member->id;
        $member->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'member.delete',
            entityType: 'member',
            entityId: $memberId,
            before: $before,
        );

        return redirect()->route('members.index')->with('status', 'Member deleted successfully.');
    }

    public function export()
    {
        $headers = ['Content-Type' => 'text/csv'];
        $filename = 'members-'.now()->format('Ymd-His').'.csv';

        $callback = function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['full_name', 'gender', 'phone', 'zone', 'residency', 'marital_status', 'email', 'member_code', 'tithe_code']);

            Member::query()->orderBy('id')->chunk(250, function ($members) use ($handle) {
                foreach ($members as $member) {
                    fputcsv($handle, [
                        $member->full_name,
                        $member->gender,
                        $member->phone,
                        $member->zone,
                        $member->residency,
                        $member->marital_status,
                        $member->email,
                        $member->member_code,
                        $member->tithe_code,
                    ]);
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        abort_if($handle === false, 422, 'Unable to read the uploaded file.');

        $headers = array_map(fn ($header) => Str::of((string) $header)->trim()->lower()->value(), fgetcsv($handle) ?: []);
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            if (! $data || blank($data['full_name'] ?? null) || blank($data['gender'] ?? null)) {
                continue;
            }

            Member::query()->create([
                'full_name' => $data['full_name'],
                'gender' => $data['gender'],
                'phone' => $data['phone'] ?: null,
                'zone' => $data['zone'] ?: null,
                'residency' => $data['residency'] ?: null,
                'marital_status' => $data['marital_status'] ?: null,
                'email' => $data['email'] ?: null,
                'member_code' => $data['member_code'] ?: null,
                'tithe_code' => $data['tithe_code'] ?: null,
            ]);

            $count++;
        }

        fclose($handle);

        $this->auditLogger->log(
            request: $request,
            action: 'member.import',
            entityType: 'member',
            after: ['records' => $count],
        );

        return redirect()->route('members.index')->with('status', $count.' member records imported successfully.');
    }

    private function normalizedMemberData(array $data, ?Member $member): array
    {
        $isMarried = ($data['marital_status'] ?? null) === 'Married';
        $partnerMemberId = $isMarried ? ($data['partner_member_id'] ?? null) : null;
        $sharePartnerTitheCode = $isMarried && ($data['share_partner_tithe_code'] ?? false);

        if ($partnerMemberId !== null && $member !== null && (int) $partnerMemberId === (int) $member->id) {
            $partnerMemberId = null;
            $sharePartnerTitheCode = false;
        }

        $partner = $partnerMemberId ? Member::query()->find($partnerMemberId, ['id', 'full_name', 'tithe_code']) : null;
        $titheCode = $data['tithe_code'] ?? ($member?->tithe_code ?? null);

        if ($member === null && blank($titheCode)) {
            $titheCode = Member::nextTitheCode();
        }

        if ($sharePartnerTitheCode && $partner?->tithe_code) {
            $titheCode = $partner->tithe_code;
        }

        return [
            'family_id' => $data['family_id'] ?? null,
            'full_name' => $data['full_name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'] ?? null,
            'tithe_code' => $titheCode,
            'share_partner_tithe_code' => $sharePartnerTitheCode,
            'zone' => $data['zone'] ?? null,
            'residency' => $data['residency'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'profile_pic' => $data['profile_pic'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'partner_member_id' => $partner?->id,
            'partner_name' => $isMarried
                ? ($partner?->full_name ?? ($data['partner_name'] ?? null))
                : null,
            'married_date' => $isMarried ? ($data['married_date'] ?? null) : null,
            'is_born_again' => $data['is_born_again'] ?? false,
            'born_again_date' => $data['born_again_date'] ?? null,
            'is_baptized' => $data['is_baptized'] ?? false,
            'baptized_date' => $data['baptized_date'] ?? null,
            'holy_spirit_baptised' => $data['holy_spirit_baptised'] ?? false,
            'membership_date' => $data['membership_date'] ?? null,
            'member_code' => $data['member_code'] ?? null,
            'username' => $data['username'] ?? null,
            'email' => $data['email'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'employment_status' => $data['employment_status'] ?? null,
            'is_university_student' => $data['is_university_student'] ?? false,
            'university_id' => ($data['is_university_student'] ?? false) ? ($data['university_id'] ?? null) : null,
            'university_start_date' => ($data['is_university_student'] ?? false) ? ($data['university_start_date'] ?? null) : null,
            'university_end_date' => ($data['is_university_student'] ?? false) ? ($data['university_end_date'] ?? null) : null,
        ];
    }

    private function syncPartnerMarriageDetails(Member $member, array $data, ?int $originalPartnerId = null): void
    {
        $currentPartnerId = $data['marital_status'] === 'Married' ? ($data['partner_member_id'] ?? null) : null;

        if ($originalPartnerId && (int) $originalPartnerId !== (int) $currentPartnerId) {
            Member::query()
                ->whereKey($originalPartnerId)
                ->where('partner_member_id', $member->id)
                ->update([
                    'partner_member_id' => null,
                    'partner_name' => null,
                    'married_date' => null,
                    'marital_status' => null,
                ]);
        }

        if (! $currentPartnerId) {
            return;
        }

        $partner = Member::query()->find($currentPartnerId, ['id', 'full_name']);

        if (! $partner) {
            return;
        }

        Member::query()
            ->whereKey($partner->id)
            ->update([
                'partner_member_id' => $member->id,
                'partner_name' => $member->full_name,
                'married_date' => $data['married_date'] ?? null,
                'marital_status' => 'Married',
            ]);
    }
}
