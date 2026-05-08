<?php

namespace App\Http\Controllers;

use App\Http\Requests\Families\StoreFamilyRequest;
use App\Http\Requests\Families\UpdateFamilyRequest;
use App\Models\Family;
use App\Models\Member;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FamilyController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $families = Family::query()
            ->withCount('members')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('head_of_family', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('gender', 'like', "%{$search}%")
                        ->orWhere('zone', 'like', "%{$search}%");
                });
            })
            ->orderBy('head_of_family')
            ->paginate(15)
            ->withQueryString();

        return view('families.index', [
            'families' => $families,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $members = Member::orderBy('full_name')->get(['id', 'full_name', 'gender']);

        return view('families.create', [
            'family'    => new Family(['gender' => 'Male']),
            'members'   => $members,
            'linkedIds' => [],
        ]);
    }

    public function store(StoreFamilyRequest $request): RedirectResponse
    {
        $family = Family::query()->create($request->safe()->except(['member_ids']));

        $memberIds = array_filter(array_map('intval', $request->input('member_ids', [])));
        if (! empty($memberIds)) {
            Member::whereIn('id', $memberIds)->update(['family_id' => $family->id]);
        }

        $this->auditLogger->log(
            request: $request,
            action: 'family.create',
            entityType: 'family',
            entityId: $family->id,
            after: Arr::only($family->toArray(), ['head_of_family', 'gender', 'phone', 'zone']),
        );

        return redirect()->route('families.show', $family)->with('status', 'Family created successfully.');
    }

    public function show(Family $family): View
    {
        $family->load(['members', 'pastoralCases.assignee', 'attendanceRecords']);

        return view('families.show', compact('family'));
    }

    public function edit(Family $family): View
    {
        $members   = Member::orderBy('full_name')->get(['id', 'full_name', 'gender']);
        $linkedIds = $family->members()->pluck('id')->toArray();

        return view('families.edit', compact('family', 'members', 'linkedIds'));
    }

    public function update(UpdateFamilyRequest $request, Family $family): RedirectResponse
    {
        $before = Arr::only($family->toArray(), ['head_of_family', 'gender', 'phone', 'zone']);
        $family->update($request->safe()->except(['member_ids']));

        $memberIds = array_filter(array_map('intval', $request->input('member_ids', [])));
        Member::where('family_id', $family->id)
            ->whereNotIn('id', empty($memberIds) ? [0] : $memberIds)
            ->update(['family_id' => null]);
        if (! empty($memberIds)) {
            Member::whereIn('id', $memberIds)->update(['family_id' => $family->id]);
        }

        $this->auditLogger->log(
            request: $request,
            action: 'family.update',
            entityType: 'family',
            entityId: $family->id,
            before: $before,
            after: Arr::only($family->fresh()->toArray(), ['head_of_family', 'gender', 'phone', 'zone']),
        );

        return redirect()->route('families.show', $family)->with('status', 'Family updated successfully.');
    }

    public function destroy(Family $family): RedirectResponse
    {
        $before = Arr::only($family->toArray(), ['head_of_family', 'gender', 'phone', 'zone']);
        $familyId = $family->id;
        $family->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'family.delete',
            entityType: 'family',
            entityId: $familyId,
            before: $before,
        );

        return redirect()->route('families.index')->with('status', 'Family deleted successfully.');
    }

    public function export()
    {
        $headers = ['Content-Type' => 'text/csv'];
        $filename = 'families-'.now()->format('Ymd-His').'.csv';

        $callback = function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['head_of_family', 'gender', 'phone', 'zone', 'address', 'home_cell_group', 'joined_date', 'remarks', 'linked_members']);

            Family::query()->withCount('members')->orderBy('id')->chunk(250, function ($families) use ($handle) {
                foreach ($families as $family) {
                    fputcsv($handle, [
                        $family->head_of_family,
                        $family->gender,
                        $family->phone,
                        $family->zone,
                        $family->address,
                        $family->home_cell_group,
                        $family->joined_date?->format('Y-m-d'),
                        $family->remarks,
                        $family->members_count,
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
            if (! $data || blank($data['head_of_family'] ?? null) || blank($data['gender'] ?? null)) {
                continue;
            }

            Family::query()->updateOrCreate(
                ['head_of_family' => $data['head_of_family'], 'phone' => $data['phone'] ?: null],
                [
                    'gender'          => ucfirst(strtolower($data['gender'])),
                    'zone'            => $data['zone'] ?? null ?: null,
                    'address'         => $data['address'] ?? null ?: null,
                    'home_cell_group' => $data['home_cell_group'] ?? null ?: null,
                    'joined_date'     => filled($data['joined_date'] ?? null) ? $data['joined_date'] : null,
                    'remarks'         => $data['remarks'] ?? null ?: null,
                ]
            );

            $count++;
        }

        fclose($handle);

        $this->auditLogger->log(
            request: $request,
            action: 'family.import',
            entityType: 'family',
            after: ['records' => $count],
        );

        return redirect()->route('families.index')->with('status', $count.' family records imported successfully.');
    }
}
