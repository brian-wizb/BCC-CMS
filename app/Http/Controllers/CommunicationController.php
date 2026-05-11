<?php

namespace App\Http\Controllers;

use App\Jobs\SendCommunicationJob;
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
            'communications' => Communication::query()
                ->withCount('deliveries')
                ->latest('id')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('communications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'channel'       => ['required', 'string', Rule::in(['sms', 'whatsapp'])],
            'audience_type' => ['required', 'string', Rule::in(['all_members', 'all_visitors', 'everyone'])],
            'subject'       => ['nullable', 'string', 'max:255'],
            'message'       => ['required', 'string'],
        ]);

        $data['status']     = 'draft';
        $data['created_by'] = auth()->id();

        $communication = Communication::query()->create($data);

        return redirect()->route('communications.show', $communication)
            ->with('status', 'Communication draft saved.');
    }

    public function show(Communication $communication): View
    {
        $deliveries = $communication->deliveries()->latest('id')->paginate(25);

        // Pre-resolve recipient names in two queries to avoid N+1.
        $memberIds   = $deliveries->where('recipient_type', 'member')->pluck('recipient_id');
        $visitorIds  = $deliveries->where('recipient_type', 'visitor')->pluck('recipient_id');
        $memberNames = Member::whereIn('id', $memberIds)->pluck('full_name', 'id');
        $visitorNames = Visitor::whereIn('id', $visitorIds)->pluck('full_name', 'id');

        // Delivery summary counts (from DB, not from paginated set).
        $stats = $communication->deliveries()
            ->selectRaw('delivery_status, COUNT(*) as cnt')
            ->groupBy('delivery_status')
            ->pluck('cnt', 'delivery_status');

        return view('communications.show', compact(
            'communication', 'deliveries', 'memberNames', 'visitorNames', 'stats'
        ));
    }

    public function update(Request $request, Communication $communication): RedirectResponse
    {
        if ($communication->status === 'sent') {
            return back()->with('error', 'Cannot edit a sent communication.');
        }

        $data = $request->validate([
            'channel'       => ['required', 'string', Rule::in(['sms', 'whatsapp'])],
            'audience_type' => ['required', 'string', Rule::in(['all_members', 'all_visitors', 'everyone'])],
            'subject'       => ['nullable', 'string', 'max:255'],
            'message'       => ['required', 'string'],
        ]);

        $communication->update($data);

        return back()->with('status', 'Draft updated successfully.');
    }

    public function destroy(Communication $communication): RedirectResponse
    {
        $communication->delete();

        return redirect()->route('communications.index')
            ->with('status', 'Communication deleted.');
    }

    public function send(Communication $communication): RedirectResponse
    {
        if ($communication->status === 'sent') {
            return back()->with('error', 'This communication has already been sent.');
        }

        // Phone-based channels only — pick the phone field for contact.
        $dispatched = 0;
        $skipped    = 0;

        $enqueue = function ($person, string $type) use ($communication, &$dispatched, &$skipped) {
            $contact = $person->phone ?? null;
            if (blank($contact)) {
                $skipped++;
                return;
            }

            $delivery = CommunicationDelivery::create([
                'communication_id' => $communication->id,
                'recipient_type'   => $type,
                'recipient_id'     => $person->id,
                'recipient_contact' => $contact,
                'delivery_status'  => 'queued',
            ]);

            SendCommunicationJob::dispatch($delivery);
            $dispatched++;
        };

        if (in_array($communication->audience_type, ['all_members', 'everyone'], true)) {
            Member::query()->select(['id', 'full_name', 'phone'])
                ->each(fn ($m) => $enqueue($m, 'member'));
        }

        if (in_array($communication->audience_type, ['all_visitors', 'everyone'], true)) {
            Visitor::query()->select(['id', 'full_name', 'phone'])
                ->each(fn ($v) => $enqueue($v, 'visitor'));
        }

        $communication->update(['status' => 'sent', 'sent_at' => now()]);

        $msg = "Queued {$dispatched} message(s) for delivery.";
        if ($skipped > 0) {
            $msg .= " {$skipped} recipient(s) skipped — no phone number on file.";
        }

        return back()->with('status', $msg);
    }

    public function retryFailed(Communication $communication): RedirectResponse
    {
        $failed = $communication->deliveries()
            ->where('delivery_status', 'failed')
            ->get();

        if ($failed->isEmpty()) {
            return back()->with('error', 'No failed deliveries to retry.');
        }

        $count = 0;
        foreach ($failed as $delivery) {
            $delivery->update([
                'delivery_status'   => 'queued',
                'provider_response' => null,
                'delivered_at'      => null,
            ]);
            SendCommunicationJob::dispatch($delivery);
            $count++;
        }

        return back()->with('status', "Re-queued {$count} failed delivery/deliveries.");
    }
}
