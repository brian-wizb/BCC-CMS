<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Service;
use App\Models\Visitor;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VisitorController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status'));

        $visitors = Visitor::query()
            ->with(['service', 'convertedMember'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('invited_by', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('visitors.index', [
            'visitors' => $visitors,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('visitors.create', [
            'visitor'  => new Visitor(['status' => 'new']),
            'services' => Service::query()->orderByDesc('service_date')->get(),
            'members'  => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $visitor = Visitor::query()->create($data);

        $this->auditLogger->log(
            request: $request,
            action: 'visitor.create',
            entityType: 'visitor',
            entityId: $visitor->id,
            after: Arr::only($visitor->toArray(), ['full_name', 'phone', 'status']),
        );

        return redirect()->route('visitors.show', $visitor)->with('status', 'Visitor captured successfully.');
    }

    public function show(Visitor $visitor): View
    {
        $visitor->load([
            'service',
            'convertedMember',
            'followUpTasks' => fn ($q) => $q->with('leader')->latest('id'),
        ]);

        return view('visitors.show', compact('visitor'));
    }

    public function edit(Visitor $visitor): View
    {
        return view('visitors.edit', [
            'visitor'  => $visitor,
            'services' => Service::query()->orderByDesc('service_date')->get(),
            'members'  => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }

    public function update(Request $request, Visitor $visitor): RedirectResponse
    {
        $before = Arr::only($visitor->toArray(), ['full_name', 'phone', 'status', 'notes']);
        $visitor->update($this->validatedData($request));

        $this->auditLogger->log(
            request: $request,
            action: 'visitor.update',
            entityType: 'visitor',
            entityId: $visitor->id,
            before: $before,
            after: Arr::only($visitor->fresh()->toArray(), ['full_name', 'phone', 'status', 'notes']),
        );

        return redirect()->route('visitors.show', $visitor)->with('status', 'Visitor updated successfully.');
    }

    public function destroy(Visitor $visitor): RedirectResponse
    {
        $visitor->delete();

        return redirect()->route('visitors.index')->with('status', 'Visitor deleted successfully.');
    }

    public function updateStatus(Request $request, Visitor $visitor): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(['new', 'contacted', 'counseled', 'joined_zone', 'in_class', 'converted'])],
        ]);

        $before = $visitor->status;
        $visitor->update(['status' => $data['status']]);

        $this->auditLogger->log(
            request: $request,
            action: 'visitor.status.update',
            entityType: 'visitor',
            entityId: $visitor->id,
            before: ['status' => $before],
            after: ['status' => $visitor->status],
        );

        return back()->with('status', 'Visitor status updated.');
    }

    public function convertToMember(Request $request, Visitor $visitor): RedirectResponse
    {
        if ($visitor->converted_member_id) {
            return back()->with('status', 'Visitor was already converted.');
        }

        $member = Member::query()->create([
            'full_name' => $visitor->full_name,
            'gender' => $visitor->gender ?: 'Unknown',
            'phone' => $visitor->phone,
            'email' => $visitor->email,
            'zone' => null,
            'residency' => $visitor->address,
            'membership_date' => now()->toDateString(),
        ]);

        $visitor->update([
            'status' => 'converted',
            'converted_member_id' => $member->id,
        ]);

        $this->auditLogger->log(
            request: $request,
            action: 'visitor.convert',
            entityType: 'visitor',
            entityId: $visitor->id,
            after: ['converted_member_id' => $member->id],
        );

        return back()->with('status', 'Visitor converted to member successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'invited_by' => ['nullable', 'string', 'max:255'],
            'first_visit_date' => ['nullable', 'date'],
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')],
            'status' => ['required', 'string', Rule::in(['new', 'contacted', 'counseled', 'joined_zone', 'in_class', 'converted'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
