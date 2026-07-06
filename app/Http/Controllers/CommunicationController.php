<?php

namespace App\Http\Controllers;

use App\Jobs\SendCommunicationJob;
use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\Member;
use App\Models\Visitor;
use Illuminate\Support\Arr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        return view('communications.create', [
            'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name', 'phone']),
            'visitors' => Visitor::query()->orderBy('full_name')->get(['id', 'full_name', 'phone']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'channel'       => ['required', 'string', Rule::in(['sms', 'whatsapp'])],
            'audience_type' => ['required', 'string', Rule::in(['all_members', 'all_visitors', 'everyone', 'individual_registered', 'individual_unregistered'])],
            'subject'       => ['nullable', 'string', 'max:255'],
            'message'       => ['required', 'string'],
            'recipient_type' => ['nullable', 'string', Rule::in(['member', 'visitor'])],
            'recipient_id' => ['nullable', 'integer'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $filters = $this->buildAudienceFilters($data);

        $data['status']     = 'draft';
        $data['created_by'] = auth()->id();
        $data['filters_json'] = $filters;

        $communication = Communication::query()->create($data);

        return redirect()->route('communications.show', $communication)
            ->with('status', 'Communication draft saved.');
    }

    public function show(Communication $communication): View
    {
        $deliveries = $communication->deliveries()->latest('id')->paginate(25);
        $individual = Arr::get($communication->filters_json, 'individual', []);

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

        return view('communications.show', [
            'communication' => $communication,
            'deliveries' => $deliveries,
            'memberNames' => $memberNames,
            'visitorNames' => $visitorNames,
            'stats' => $stats,
            'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name', 'phone']),
            'visitors' => Visitor::query()->orderBy('full_name')->get(['id', 'full_name', 'phone']),
            'individual' => $individual,
        ]);
    }

    public function update(Request $request, Communication $communication): RedirectResponse
    {
        if ($communication->status === 'sent') {
            return back()->with('error', 'Cannot edit a sent communication.');
        }

        $data = $request->validate([
            'channel'       => ['required', 'string', Rule::in(['sms', 'whatsapp'])],
            'audience_type' => ['required', 'string', Rule::in(['all_members', 'all_visitors', 'everyone', 'individual_registered', 'individual_unregistered'])],
            'subject'       => ['nullable', 'string', 'max:255'],
            'message'       => ['required', 'string'],
            'recipient_type' => ['nullable', 'string', Rule::in(['member', 'visitor'])],
            'recipient_id' => ['nullable', 'integer'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $audienceType = $data['audience_type'] ?? null;
        $hasRecipientInput = $request->hasAny([
            'recipient_type',
            'recipient_id',
            'recipient_name',
            'recipient_contact_phone',
        ]);

        if (in_array($audienceType, ['individual_registered', 'individual_unregistered'], true) && ! $hasRecipientInput) {
            $data['filters_json'] = (array) ($communication->filters_json ?? []);
        } else {
            $data['filters_json'] = $this->buildAudienceFilters($data);
        }

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

        if ($communication->audience_type === 'individual_registered') {
            $individual = Arr::get($communication->filters_json, 'individual', []);
            $recipientType = $individual['recipient_type'] ?? null;
            $recipientId = isset($individual['recipient_id']) ? (int) $individual['recipient_id'] : null;

            if ($recipientType === 'member' && $recipientId) {
                $member = Member::query()->select(['id', 'full_name', 'phone'])->find($recipientId);
                if ($member) {
                    $enqueue($member, 'member');
                }
            }

            if ($recipientType === 'visitor' && $recipientId) {
                $visitor = Visitor::query()->select(['id', 'full_name', 'phone'])->find($recipientId);
                if ($visitor) {
                    $enqueue($visitor, 'visitor');
                }
            }
        }

        if ($communication->audience_type === 'individual_unregistered') {
            $individual = Arr::get($communication->filters_json, 'individual', []);
            $contact = $individual['recipient_contact_phone'] ?? null;

            if (blank($contact)) {
                $skipped++;
            } else {
                $delivery = CommunicationDelivery::create([
                    'communication_id' => $communication->id,
                    'recipient_type' => 'manual',
                    'recipient_id' => 0,
                    'recipient_contact' => $contact,
                    'delivery_status' => 'queued',
                ]);
                SendCommunicationJob::dispatch($delivery);
                $dispatched++;
            }
        }

        $communication->update(['status' => 'sent', 'sent_at' => now()]);

        $msg = "Queued {$dispatched} message(s) for delivery.";
        if ($skipped > 0) {
            $msg .= " {$skipped} recipient(s) skipped — no phone number on file.";
        }

        return back()->with('status', $msg);
    }

    private function buildAudienceFilters(array $data): array
    {
        $audienceType = $data['audience_type'] ?? null;
        $filters = [];

        if ($audienceType === 'individual_registered') {
            $recipientType = $data['recipient_type'] ?? null;
            $recipientId = isset($data['recipient_id']) ? (int) $data['recipient_id'] : null;

            if (! in_array($recipientType, ['member', 'visitor'], true) || ! $recipientId) {
                throw ValidationException::withMessages([
                    'recipient_id' => 'Choose a valid registered recipient.',
                ]);
            }

            if ($recipientType === 'member' && ! Member::query()->whereKey($recipientId)->exists()) {
                throw ValidationException::withMessages([
                    'recipient_id' => 'Selected member does not exist.',
                ]);
            }

            if ($recipientType === 'visitor' && ! Visitor::query()->whereKey($recipientId)->exists()) {
                throw ValidationException::withMessages([
                    'recipient_id' => 'Selected visitor does not exist.',
                ]);
            }

            $filters['individual'] = [
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
            ];
        }

        if ($audienceType === 'individual_unregistered') {
            $name = trim((string) ($data['recipient_name'] ?? ''));
            $phone = trim((string) ($data['recipient_contact_phone'] ?? ''));

            if ($name === '' || $phone === '') {
                throw ValidationException::withMessages([
                    'recipient_contact_phone' => 'Provide recipient name and phone for unregistered recipient.',
                ]);
            }

            $filters['individual'] = [
                'recipient_name' => $name,
                'recipient_contact_phone' => $phone,
            ];
        }

        return $filters;
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
