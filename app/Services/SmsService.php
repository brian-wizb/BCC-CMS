<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

class SmsService
{
    private ?Client $client = null;
    private ?string $from = null;
    private string $provider;

    public function __construct()
    {
        $this->provider = (string) config('services.sms.provider', 'beem');

        if ($this->provider === 'twilio') {
            $this->client = new Client(
                config('services.twilio.sid'),
                config('services.twilio.auth_token')
            );
            $this->from = config('services.twilio.sms_from');
        }
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
        return $this->provider === 'beem'
            ? $this->sendViaBeem($toPhone, $body)
            : $this->sendViaTwilio($toPhone, $body);
    }

    private function sendViaTwilio(string $toPhone, string $body): string
    {
        if (! $this->client || ! $this->from) {
            throw new \RuntimeException('Twilio SMS is not configured.');
        }

        $normalised = WhatsAppService::normalisePhone($toPhone);

        $message = $this->client->messages->create(
            $normalised,
            [
                'from' => $this->from,
                'body' => $body,
            ]
        );

        return (string) $message->sid;
    }

    private function sendViaBeem(string $toPhone, string $body): string
    {
        $apiKey = (string) config('services.beem.api_key');
        $secret = (string) config('services.beem.secret_key');

        if ($apiKey === '' || $secret === '') {
            throw new \RuntimeException('Beem SMS credentials are not configured.');
        }

        $baseUrl = rtrim((string) config('services.beem.base_url', 'https://apisms.beem.africa'), '/');
        $sender = (string) config('services.beem.sender_id', 'INFO');
        $verifySsl = filter_var(config('services.beem.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);
        $caBundle = config('services.beem.ca_bundle');

        $normalised = WhatsAppService::normalisePhone($toPhone);
        $destAddr = ltrim($normalised, '+');

        $sendRequest = function (string $senderId) use ($apiKey, $secret, $baseUrl, $body, $destAddr, $verifySsl, $caBundle) {
            $payload = [
                'source_addr' => $senderId,
                'encoding' => 0,
                'schedule_time' => '',
                'message' => $body,
                'recipients' => [
                    [
                        'recipient_id' => '1',
                        'dest_addr' => $destAddr,
                    ],
                ],
            ];

            $request = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':' . $secret),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(20);

            if (is_string($caBundle) && trim($caBundle) !== '') {
                $request = $request->withOptions(['verify' => $caBundle]);
            } else {
                $request = $request->withOptions(['verify' => $verifySsl]);
            }

            return $request->post($baseUrl . '/v1/send', $payload);
        };

        $response = $sendRequest($sender);

        if (! $response->successful()) {
            throw new \RuntimeException('Beem SMS request failed: ' . $response->status() . ' ' . $response->body());
        }

        $json = $response->json();

        if (is_array($json)) {
            $messageId = data_get($json, 'data.0.message_id')
                ?? data_get($json, 'messages.0.message_id')
                ?? data_get($json, 'message_id')
                ?? data_get($json, 'response.0.message_id');

            if ($messageId) {
                return (string) $messageId;
            }
        }

        return (string) $response->body();
    }
}
