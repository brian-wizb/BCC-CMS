<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    private Client $client;
    private string $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
        $this->from = config('services.twilio.whatsapp_from');
    }

    /**
     * Send a plain-text WhatsApp message to a phone number.
     * This is the general-purpose method used by bulk communications.
     *
     * @param  string  $toPhone  Any accepted format: +255693228630 / 0693228630 / 255693228630
     * @param  string  $body     Message text
     * @return string  Twilio message SID
     */
    public function send(string $toPhone, string $body): string
    {
        $normalised = self::normalisePhone($toPhone);

        $message = $this->client->messages->create(
            'whatsapp:' . $normalised,
            [
                'from' => $this->from,
                'body' => $body,
            ]
        );

        return $message->sid;
    }

    /**
     * Send a QR code image to a WhatsApp number.
     *
     * @param  string  $toPhone   E.164 phone number, e.g. +255693228630
     * @param  string  $name      Person's name
     * @param  string  $qrToken   The raw QR token string to encode
     * @return string  Twilio message SID
     */
    public function sendQrCode(string $toPhone, string $name, string $qrToken): string
    {
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/'
            . '?size=400x400'
            . '&margin=10'
            . '&data=' . urlencode($qrToken);

        $appName = config('app.name', 'BCC');
        $normalised = self::normalisePhone($toPhone);

        $message = $this->client->messages->create(
            'whatsapp:' . $normalised,
            [
                'from'     => $this->from,
                'body'     => "Hello {$name}! 👋\n\n"
                    . "Here is your personal attendance QR code for *{$appName}*.\n\n"
                    . "📲 *Save this to your phone.* Show it to the usher at the entrance of each service — your attendance will be recorded automatically.\n\n"
                    . "_Do not share this code with others._",
                'mediaUrl' => [$qrImageUrl],
            ]
        );

        return $message->sid;
    }

    /**
     * Normalise phone to WhatsApp format.
     * Accepts: 0693228630, +255693228630, 255693228630
     * Returns: whatsapp:+255693228630
     */
    public static function normalisePhone(string $phone, string $countryCode = '255'): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = $countryCode . substr($digits, 1);
        } elseif (! str_starts_with($digits, $countryCode)) {
            $digits = $countryCode . $digits;
        }

        return '+' . $digits;
    }
}
