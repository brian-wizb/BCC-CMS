<?php

namespace App\Http\Controllers;

use App\Models\ChildrenMinistry;
use App\Models\Member;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ChildrenMinistryController extends Controller
{
    private AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display a listing of the children.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $dateFrom = $request->filled('date_from') ? $request->string('date_from')->toString() : null;
        $dateTo = $request->filled('date_to') ? $request->string('date_to')->toString() : null;

        $children = ChildrenMinistry::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('parent_name', 'like', "%{$search}%")
                        ->orWhere('parent_contact', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom !== null, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo !== null, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('children-ministry.index', [
            'children' => $children,
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Export children ministry records as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $search = trim((string) $request->string('search'));
        $dateFrom = $request->filled('date_from') ? $request->string('date_from')->toString() : null;
        $dateTo = $request->filled('date_to') ? $request->string('date_to')->toString() : null;

        return Response::streamDownload(function () use ($search, $dateFrom, $dateTo): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['full_name', 'date_of_birth', 'sex', 'parent_name', 'parent_contact', 'linked_member', 'remarks', 'added_date']);

            ChildrenMinistry::query()
                ->with('parentMember:id,full_name')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('parent_name', 'like', "%{$search}%")
                            ->orWhere('parent_contact', 'like', "%{$search}%");
                    });
                })
                ->when($dateFrom !== null, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo !== null, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
                ->orderBy('id')
                ->chunk(250, function ($children) use ($handle): void {
                    foreach ($children as $child) {
                        fputcsv($handle, [
                            $child->full_name,
                            optional($child->date_of_birth)->toDateString(),
                            $child->sex,
                            $child->parent_name,
                            $child->parent_contact,
                            $child->parentMember?->full_name,
                            $child->remarks,
                            optional($child->created_at)->toDateString(),
                        ]);
                    }
                });

            fclose($handle);
        }, 'children-ministry-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Show the form for creating a new child.
     */
    public function create(): View
    {
        $members = Member::query()
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return view('children-ministry.create', compact('members'));
    }

    /**
     * Store a newly created child in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $child = ChildrenMinistry::create($validated);

        $this->auditLogger->log($request, 'children_ministry.create', 'children_ministry', $child->id);

        return redirect()->route('children-ministry.show', $child)
            ->with('success', "Child '{$child->full_name}' added to Children Ministry.");
    }

    /**
     * Display the specified child.
     */
    public function show(ChildrenMinistry $childrenMinistry): View
    {
        $childrenMinistry->load('parentMember');

        return view('children-ministry.show', [
            'child' => $childrenMinistry,
        ]);
    }

    /**
     * Show the form for editing the specified child.
     */
    public function edit(ChildrenMinistry $childrenMinistry): View
    {
        $members = Member::query()
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return view('children-ministry.edit', [
            'child' => $childrenMinistry,
            'members' => $members,
        ]);
    }

    /**
     * Update the specified child in storage.
     */
    public function update(Request $request, ChildrenMinistry $childrenMinistry): RedirectResponse
    {
        $validated = $this->validatedData($request, $childrenMinistry);

        $childrenMinistry->update($validated);

        $this->auditLogger->log($request, 'children_ministry.update', 'children_ministry', $childrenMinistry->id);

        return redirect()->route('children-ministry.show', $childrenMinistry)
            ->with('success', "Child '{$childrenMinistry->full_name}' updated successfully.");
    }

    private function validatedData(Request $request, ?ChildrenMinistry $child = null): array
    {
        $request->merge([
            'first_name' => trim((string) $request->input('first_name')),
            'middle_name' => trim((string) $request->input('middle_name')),
            'surname' => trim((string) $request->input('surname')),
            'parent_name' => trim((string) $request->input('parent_name')),
            'parent_contact' => trim((string) $request->input('parent_contact')),
        ]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'sex' => ['required', 'in:Male,Female'],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_contact' => ['nullable', 'string', 'max:20'],
            'parent_member_id' => ['nullable', 'exists:members,id'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $duplicateQuery = ChildrenMinistry::query()
            ->when($child, fn ($query) => $query->whereKeyNot($child->id))
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [strtolower($validated['first_name'])])
            ->whereRaw("LOWER(TRIM(COALESCE(middle_name, ''))) = ?", [strtolower($validated['middle_name'] ?? '')])
            ->whereRaw('LOWER(TRIM(surname)) = ?', [strtolower($validated['surname'])]);

        if (filled($validated['date_of_birth'] ?? null)) {
            $duplicateQuery->whereDate('date_of_birth', $validated['date_of_birth']);
        } elseif (filled($validated['parent_member_id'] ?? null)) {
            $duplicateQuery
                ->whereNull('date_of_birth')
                ->where('parent_member_id', $validated['parent_member_id']);
        } elseif (filled($validated['parent_contact'] ?? null)) {
            $duplicateQuery
                ->whereNull('date_of_birth')
                ->where('parent_contact', $validated['parent_contact']);
        } else {
            $duplicateQuery->whereRaw('1 = 0');
        }

        if ($duplicateQuery->exists()) {
            throw ValidationException::withMessages([
                'date_of_birth' => 'A child with the same name and date of birth already exists.',
            ]);
        }

        return $validated;
    }

    /**
     * Remove the specified child from storage.
     */
    public function destroy(Request $request, ChildrenMinistry $childrenMinistry): RedirectResponse
    {
        $childName = $childrenMinistry->full_name;
        $childId = $childrenMinistry->id;
        $childrenMinistry->delete();

        $this->auditLogger->log($request, 'children_ministry.delete', 'children_ministry', $childId);

        return redirect()->route('children-ministry.index')
            ->with('success', "Child '{$childName}' removed from Children Ministry.");
    }
}
