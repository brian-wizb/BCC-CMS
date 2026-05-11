<?php

namespace App\Jobs;

use App\Models\CommunicationDelivery;
use App\Services\SmsService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Twilio\Exceptions\RestException;

class SendCommunicationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Retry up to 3 times with a 60-second back-off between attempts.
     */
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(private readonly CommunicationDelivery $delivery) {}

    public function handle(WhatsAppService $whatsApp, SmsService $sms): void
    {
        $comm    = $this->delivery->communication;
        $contact = $this->delivery->recipient_contact;

        // Nothing to send to — mark failed immediately, no retry needed.
        if (blank($contact)) {
            $this->delivery->update([
                'delivery_status'   => 'failed',
                'provider_response' => 'No contact number on file.',
            ]);
            $this->fail('No contact number on file.');
            return;
        }

        try {
            $sid = match ($comm->channel) {
                'sms'       => $sms->send($contact, $comm->message),
                'whatsapp'  => $whatsApp->send($contact, $comm->message),
                default     => throw new \RuntimeException("Unsupported channel: {$comm->channel}"),
            };

            $this->delivery->update([
                'delivery_status'   => 'delivered',
                'provider_response' => $sid,
                'delivered_at'      => now(),
            ]);
        } catch (\Throwable $e) {
            // Record the error so it shows in the delivery table.
            $this->delivery->update([
                'delivery_status'   => 'failed',
                'provider_response' => $e->getMessage(),
            ]);

            // For permanent Twilio errors (rate limit, invalid number, etc.)
            // there is no point retrying — fail immediately.
            if ($e instanceof RestException && in_array($e->getStatusCode(), [400, 429])) {
                $this->fail($e);
                return;
            }

            // Re-throw so the queue runner retries (up to $tries times).
            throw $e;
        }
    }
}
