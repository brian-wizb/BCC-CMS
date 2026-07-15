<?php

namespace App\Http\Controllers;

use App\Jobs\SendCommunicationJob;
use App\Models\Communication;
use App\Models\CommunicationCreditSetting;
use App\Models\CommunicationDelivery;
use App\Models\Donation;
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
        $smsSentCount = $this->usedSmsCount();

        return view('communications.index', [
            'communications' => Communication::query()
                ->withCount('deliveries')
                ->latest('id')
                ->paginate(10),
            'smsSentCount' => $smsSentCount,
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
        $data['estimated_sms_count'] = $this->estimateSmsCount(
            channel: $data['channel'],
            message: $data['message'],
            audienceType: $data['audience_type'],
            filters: $filters,
        );
        $data['actual_sms_count'] = 0;

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
            'defaultDeliveryMode' => $this->configuredDeliveryMode(),
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

        $data['estimated_sms_count'] = $this->estimateSmsCount(
            channel: $data['channel'],
            message: $data['message'],
            audienceType: $data['audience_type'],
            filters: (array) ($data['filters_json'] ?? []),
        );

        $communication->update($data);

        return back()->with('status', 'Draft updated successfully.');
    }

    public function destroy(Communication $communication): RedirectResponse
    {
        $communication->delete();

        return redirect()->route('communications.index')
            ->with('status', 'Communication deleted.');
    }

    public function send(Request $request, Communication $communication): RedirectResponse
    {
        if ($communication->status === 'sent') {
            return back()->with('error', 'This communication has already been sent.');
        }

        $estimatedSmsCount = $this->estimateSmsCount(
            channel: $communication->channel,
            message: (string) $communication->message,
            audienceType: $communication->audience_type,
            filters: (array) ($communication->filters_json ?? []),
        );

        if ($communication->channel === 'sms') {
            $creditState = $this->creditState();
            if ($estimatedSmsCount > $creditState['remaining']) {
                return back()->with('error', "Not enough message credits. Required {$estimatedSmsCount}, remaining {$creditState['remaining']}.");
            }
        }

        $validated = $request->validate([
            'delivery_mode' => ['nullable', 'string', Rule::in(['default', 'queued', 'immediate'])],
        ]);

        $deliveryMode = $this->resolveDeliveryModeFromRequest($validated['delivery_mode'] ?? 'default');
        $immediateMode = $this->isImmediateDeliveryMode($deliveryMode);

        // Phone-based channels only — pick the phone field for contact.
        $dispatched = 0;
        $skipped    = 0;

        $enqueue = function ($person, string $type) use ($communication, $deliveryMode, &$dispatched, &$skipped) {
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

            $this->dispatchDelivery($delivery, $deliveryMode);
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
                $this->dispatchDelivery($delivery, $deliveryMode);
                $dispatched++;
            }
        }

        $actualSmsCount = $communication->channel === 'sms' ? $dispatched : 0;

        $communication->update([
            'status' => 'sent',
            'sent_at' => now(),
            'estimated_sms_count' => $estimatedSmsCount,
            'actual_sms_count' => $actualSmsCount,
        ]);

        $msg = $immediateMode
            ? "Sent {$dispatched} message(s)."
            : "Queued {$dispatched} message(s) for delivery.";
        if ($skipped > 0) {
            $msg .= " {$skipped} recipient(s) skipped — no phone number on file.";
        }

        return back()->with('status', $msg);
    }

    public function operations(): View
    {
        $creditState = $this->creditState();
        $smsBreakdown = $this->smsUsageBreakdown();

        return view('communications.operations', [
            'creditState' => $creditState,
            'smsBreakdown' => $smsBreakdown,
        ]);
    }

    public function updateOperations(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'credits_purchased_total' => ['required', 'integer', 'min:0'],
            'low_balance_threshold' => ['required', 'integer', 'min:0'],
        ]);

        $settings = CommunicationCreditSetting::query()->first();
        if (! $settings) {
            $settings = CommunicationCreditSetting::query()->create([
                'credits_purchased_total' => (int) $data['credits_purchased_total'],
                'low_balance_threshold' => (int) $data['low_balance_threshold'],
                'updated_by' => auth()->id(),
            ]);
        } else {
            $settings->update([
                'credits_purchased_total' => (int) $data['credits_purchased_total'],
                'low_balance_threshold' => (int) $data['low_balance_threshold'],
                'updated_by' => auth()->id(),
            ]);
        }

        return back()->with('status', 'Message credit settings updated successfully.');
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

    public function retryFailed(Request $request, Communication $communication): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_mode' => ['nullable', 'string', Rule::in(['default', 'queued', 'immediate'])],
        ]);

        $deliveryMode = $this->resolveDeliveryModeFromRequest($validated['delivery_mode'] ?? 'default');

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
            $this->dispatchDelivery($delivery, $deliveryMode);
            $count++;
        }

        $message = $this->isImmediateDeliveryMode($deliveryMode)
            ? "Retried {$count} failed delivery/deliveries immediately."
            : "Re-queued {$count} failed delivery/deliveries.";

        return back()->with('status', $message);
    }

    private function dispatchDelivery(CommunicationDelivery $delivery, ?string $mode = null): void
    {
        if (! $this->isImmediateDeliveryMode($mode)) {
            SendCommunicationJob::dispatch($delivery);
            return;
        }

        try {
            SendCommunicationJob::dispatchSync($delivery);
        } catch (\Throwable) {
            // Job already records failure details on the delivery row.
        }
    }

    private function resolveDeliveryModeFromRequest(string $requested): string
    {
        return match ($requested) {
            'queued', 'immediate' => $requested,
            default => $this->configuredDeliveryMode(),
        };
    }

    private function configuredDeliveryMode(): string
    {
        $mode = strtolower((string) config('communications.delivery_mode', 'queued'));

        return in_array($mode, ['queued', 'immediate', 'auto', 'sync'], true)
            ? $mode
            : 'queued';
    }

    private function isImmediateDeliveryMode(?string $mode = null): bool
    {
        $mode = strtolower((string) ($mode ?? $this->configuredDeliveryMode()));

        if (in_array($mode, ['immediate', 'sync'], true)) {
            return true;
        }

        if ($mode === 'auto') {
            return (string) config('queue.default') === 'sync';
        }

        return false;
    }

    private function estimateSmsCount(string $channel, string $message, string $audienceType, array $filters = []): int
    {
        if ($channel !== 'sms') {
            return 0;
        }

        $messageLength = mb_strlen($message);
        if ($messageLength <= 0) {
            return 0;
        }

        $partsPerRecipient = $messageLength <= 160 ? 1 : (int) ceil($messageLength / 153);

        $recipientCount = match ($audienceType) {
            'all_members' => (int) Member::query()->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'all_visitors' => (int) Visitor::query()->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'everyone' => (int) Member::query()->whereNotNull('phone')->where('phone', '!=', '')->count()
                + (int) Visitor::query()->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'individual_registered' => $this->countRegisteredIndividualRecipient($filters),
            'individual_unregistered' => $this->countUnregisteredIndividualRecipient($filters),
            default => 0,
        };

        return $recipientCount * $partsPerRecipient;
    }

    private function countRegisteredIndividualRecipient(array $filters): int
    {
        $individual = Arr::get($filters, 'individual', []);
        $recipientType = $individual['recipient_type'] ?? null;
        $recipientId = isset($individual['recipient_id']) ? (int) $individual['recipient_id'] : 0;

        if (! $recipientId || ! in_array($recipientType, ['member', 'visitor'], true)) {
            return 0;
        }

        if ($recipientType === 'member') {
            return Member::query()
                ->whereKey($recipientId)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->exists() ? 1 : 0;
        }

        return Visitor::query()
            ->whereKey($recipientId)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->exists() ? 1 : 0;
    }

    private function countUnregisteredIndividualRecipient(array $filters): int
    {
        $phone = trim((string) Arr::get($filters, 'individual.recipient_contact_phone', ''));

        return $phone === '' ? 0 : 1;
    }

    private function creditState(): array
    {
        $settings = CommunicationCreditSetting::query()->first();
        $purchased = (int) ($settings?->credits_purchased_total ?? 0);
        $threshold = (int) ($settings?->low_balance_threshold ?? 100);
        $used = $this->usedSmsCount();
        $remaining = max($purchased - $used, 0);

        return [
            'purchased' => $purchased,
            'used' => $used,
            'remaining' => $remaining,
            'threshold' => $threshold,
            'is_low' => $remaining <= $threshold,
        ];
    }

    private function usedSmsCount(): int
    {
        return $this->smsUsageBreakdown()['total'];
    }

    private function smsUsageBreakdown(): array
    {
        $communicationSmsCount = (int) CommunicationDelivery::query()
            ->whereHas('communication', fn ($q) => $q->where('channel', 'sms'))
            ->whereIn('delivery_status', ['queued', 'delivered', 'failed'])
            ->count();

        // Donation receipt SMS are sent directly from DonationController and must be included in credit usage.
        $donationSmsCount = (int) Donation::query()
            ->where('sms_delivery_status', 'sent')
            ->count();

        return [
            'communications' => $communicationSmsCount,
            'donations' => $donationSmsCount,
            'total' => $communicationSmsCount + $donationSmsCount,
        ];
    }
}
