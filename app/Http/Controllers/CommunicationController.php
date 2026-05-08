<?php

namespace App\Http\Controllers;

use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\Member;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CommunicationController extends Controller
{
    public function index(): View
    {
        return view('communications.index', [
            'communications' => Communication::query()->latest('id')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('communications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'channel' => ['required', 'string', Rule::in(['sms', 'email', 'whatsapp', 'internal'])],
            'audience_type' => ['required', 'string', Rule::in(['all_members', 'all_visitors', 'everyone'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $data['status'] = 'draft';
        $data['created_by'] = auth()->id();

        $communication = Communication::query()->create($data);

        return redirect()->route('communications.show', $communication)->with('status', 'Communication draft created.');
    }

    public function show(Communication $communication): View
    {
        return view('communications.show', [
            'communication' => $communication->load('deliveries'),
        ]);
    }

    public function update(Request $request, Communication $communication): RedirectResponse
    {
        if ($communication->status === 'sent') {
            return back()->with('status', 'Cannot edit a sent communication.');
        }

        $data = $request->validate([
            'channel' => ['required', 'string', Rule::in(['sms', 'email', 'whatsapp', 'internal'])],
            'audience_type' => ['required', 'string', Rule::in(['all_members', 'all_visitors', 'everyone'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $communication->update($data);

        return back()->with('status', 'Communication updated successfully.');
    }

    public function destroy(Communication $communication): RedirectResponse
    {
        $communication->delete();

        return redirect()->route('communications.index')->with('status', 'Communication deleted successfully.');
    }

    public function send(Communication $communication): RedirectResponse
    {
        if ($communication->status === 'sent') {
            return back()->with('status', 'Communication already sent.');
        }

        $deliveries = collect();

        if (in_array($communication->audience_type, ['all_members', 'everyone'], true)) {
            $deliveries = $deliveries->merge(
                Member::query()->get(['id', 'phone', 'email'])->map(fn (Member $member) => [
                    'recipient_type' => 'member',
                    'recipient_id' => $member->id,
                    'recipient_contact' => $member->phone ?: $member->email,
                ])
            );
        }

        if (in_array($communication->audience_type, ['all_visitors', 'everyone'], true)) {
            $deliveries = $deliveries->merge(
                Visitor::query()->get(['id', 'phone', 'email'])->map(fn (Visitor $visitor) => [
                    'recipient_type' => 'visitor',
                    'recipient_id' => $visitor->id,
                    'recipient_contact' => $visitor->phone ?: $visitor->email,
                ])
            );
        }

        foreach ($deliveries as $delivery) {
            CommunicationDelivery::query()->create([
                'communication_id' => $communication->id,
                'recipient_type' => $delivery['recipient_type'],
                'recipient_id' => $delivery['recipient_id'],
                'recipient_contact' => $delivery['recipient_contact'],
                'delivery_status' => 'queued',
            ]);
        }

        $communication->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('status', 'Communication sent and deliveries generated.');
    }
}
