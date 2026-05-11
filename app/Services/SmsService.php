<?php

namespace App\Services;

use Twilio\Rest\Client;

class SmsService
{
    private Client $client;
    private string $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
        $this->from = config('services.twilio.sms_from');
    }

    /**
     * Send an SMS to a phone number.
     *
     * @param  string  $toPhone  Any accepted format: +255693228630 / 0693228630 / 255693228630
     * @param  string  $body     Message text (keep under 160 chars to avoid multi-part billing)
     * @return string  Twilio message SID
     */
    public function send(string $toPhone, string $body): string
    {
        $normalised = WhatsAppService::normalisePhone($toPhone);

        $message = $this->client->messages->create(
            $normalised,
            [
                'from' => $this->from,
                'body' => $body,
            ]
        );

        return $message->sid;
    }
}
