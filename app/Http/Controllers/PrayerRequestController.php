<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\PrayerRequest;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PrayerRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->string('status'));

        $requests = PrayerRequest::query()
            ->with(['member', 'visitor', 'assignee'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('prayer-requests.index', [
            'requests' => $requests,
            'status' => $status,
            'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name']),
            'visitors' => Visitor::query()->orderBy('full_name')->get(['id', 'full_name']),
            'users' => User::query()->orderBy('full_name')->get(['id', 'full_name', 'username']),
        ]);
    }

    public function show(PrayerRequest $prayer_request): View
    {
        return view('prayer-requests.show', [
            'requestItem' => $prayer_request->load(['member', 'visitor', 'assignee']),
            'users' => User::query()->orderBy('full_name')->get(['id', 'full_name', 'username']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        PrayerRequest::query()->create($this->validatedData($request));

        return back()->with('status', 'Prayer request created successfully.');
    }

    public function update(Request $request, PrayerRequest $prayer_request): RedirectResponse
    {
        $prayer_request->update($this->validatedData($request, true));

        return back()->with('status', 'Prayer request updated successfully.');
    }

    public function destroy(PrayerRequest $prayer_request): RedirectResponse
    {
        $prayer_request->delete();

        return redirect()->route('prayer-requests.index')->with('status', 'Prayer request deleted successfully.');
    }

    private function validatedData(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'member_id' => ['nullable', 'integer', Rule::exists('members', 'id')],
            'visitor_id' => ['nullable', 'integer', Rule::exists('visitors', 'id')],
            'request_type' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'request_text' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'visibility' => ['required', 'string', Rule::in(['private', 'leadership', 'public'])],
            'status' => ['required', 'string', Rule::in(['open', 'in_progress', 'answered', 'closed'])],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ]);
    }
}
