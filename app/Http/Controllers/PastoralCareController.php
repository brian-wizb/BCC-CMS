<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Leader;
use App\Models\Member;
use App\Models\PastoralCase;
use App\Models\PastoralCaseNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PastoralCareController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->string('status'));
        $priority = trim((string) $request->string('priority'));
        $caseType = trim((string) $request->string('case_type'));

        $cases = PastoralCase::query()
            ->with(['member', 'family', 'assignee'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($priority !== '', fn ($query) => $query->where('priority', $priority))
            ->when($caseType !== '', fn ($query) => $query->where('case_type', $caseType))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pastoral-care.index', [
            'cases' => $cases,
            'status' => $status,
            'priority' => $priority,
            'caseType' => $caseType,
        ]);
    }

    public function create(): View
    {
        return view('pastoral-care.create', [
            'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
            'families' => Family::query()->orderBy('head_of_family')->get(['id', 'head_of_family']),
            'leaders' => Leader::query()->where('status', 'active')->orderBy('full_name')->get(['id', 'full_name', 'role']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['nullable', 'integer', Rule::exists('members', 'id')],
            'family_id' => ['nullable', 'integer', Rule::exists('families', 'id')],
            'case_type' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'status' => ['required', 'string', Rule::in(['open', 'in_progress', 'answered', 'closed'])],
            'assigned_to' => ['nullable', 'integer', Rule::exists('leaders', 'id')],
            'summary' => ['nullable', 'string'],
        ]);

        $data['opened_at'] = now();
        $data['closed_at'] = $data['status'] === 'closed' ? now() : null;

        $case = PastoralCase::query()->create($data);

        return redirect()->route('pastoral-care.show', $case)->with('status', 'Care request created successfully.');
    }

    public function show(PastoralCase $pastoral_case): View
    {
        return view('pastoral-care.show', [
            'case' => $pastoral_case->load(['member', 'family', 'assignee', 'notes.creator']),
            'leaders' => Leader::query()->where('status', 'active')->orderBy('full_name')->get(['id', 'full_name', 'role']),
        ]);
    }

    public function update(Request $request, PastoralCase $pastoral_case): RedirectResponse
    {
        $data = $request->validate([
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'status' => ['required', 'string', Rule::in(['open', 'in_progress', 'answered', 'closed'])],
            'assigned_to' => ['nullable', 'integer', Rule::exists('leaders', 'id')],
            'summary' => ['nullable', 'string'],
        ]);

        $data['closed_at'] = $data['status'] === 'closed' ? now() : null;
        $pastoral_case->update($data);

        return back()->with('status', 'Care request updated successfully.');
    }

    public function destroy(PastoralCase $pastoral_case): RedirectResponse
    {
        $pastoral_case->delete();

        return redirect()->route('pastoral-care.index')->with('status', 'Care request deleted successfully.');
    }

    public function storeNote(Request $request, PastoralCase $pastoral_case): RedirectResponse
    {
        $data = $request->validate([
            'note' => ['required', 'string'],
            'visibility' => ['required', 'string', Rule::in(['private', 'public'])],
        ]);

        PastoralCaseNote::query()->create([
            'case_id' => $pastoral_case->id,
            'note' => $data['note'],
            'visibility' => $data['visibility'],
            'created_by' => auth()->id(),
        ]);

        return back()->with('status', 'Case note added successfully.');
    }
}
