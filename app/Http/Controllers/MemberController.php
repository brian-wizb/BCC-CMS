<?php

namespace App\Http\Controllers;

use App\Http\Requests\Members\StoreMemberRequest;
use App\Http\Requests\Members\UpdateMemberRequest;
use App\Models\Family;
use App\Models\Member;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

        $members = Member::query()
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
        ]);
    }

    public function create(): View
    {
        $families = Family::query()->orderBy('head_of_family')->get(['id', 'head_of_family']);

        return view('members.create', [
            'member' => new Member([
                'is_born_again' => false,
                'is_baptized' => false,
                'holy_spirit_baptised' => false,
            ]),
            'families' => $families,
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $member = Member::query()->create($this->normalizedMemberData($request->validated()));

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
        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $families = Family::query()->orderBy('head_of_family')->get(['id', 'head_of_family']);

        return view('members.edit', compact('member', 'families'));
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $before = Arr::only($member->toArray(), ['full_name', 'gender', 'phone', 'zone', 'residency']);
        $member->update($this->normalizedMemberData($request->validated()));

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

    private function normalizedMemberData(array $data): array
    {
        return [
            'full_name' => $data['full_name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'] ?? null,
            'tithe_code' => $data['tithe_code'] ?? null,
            'zone' => $data['zone'] ?? null,
            'residency' => $data['residency'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'profile_pic' => $data['profile_pic'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'partner_name' => $data['partner_name'] ?? null,
            'married_date' => $data['married_date'] ?? null,
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
        ];
    }
}
