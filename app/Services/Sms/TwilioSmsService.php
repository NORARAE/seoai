<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class TwilioSmsService
{
    private TwilioClient $client;
    private string $from;

    public function __construct()
    {
        $this->client = new TwilioClient(
            config('services.twilio.sid'),
            config('services.twilio.token'),
        );
        $this->from = config('services.twilio.from');
    }

    /**
     * Send an SMS message.
     * Throws on failure so the calling job can retry.
     */
    public function send(string $to, string $body): void
    {
        $normalized = $this->normalizePhone($to);

        $this->client->messages->create($normalized, [
            'from' => $this->from,
            'body' => $body,
        ]);

        Log::channel('booking')->info('SMS sent', ['to' => $normalized]);
    }

    /**
     * Normalise a phone number to E.164 format (+1XXXXXXXXXX for US/CA).
     * Strips all non-digit characters first, then applies a best-effort
     * country code assumption of +1 when no international prefix is present.
     */
    public function normalizePhone(string $phone): string
    {
        // Strip everything except digits and leading +
        $digits = preg_replace('/[^\d+]/', '', $phone);

        // Already has a + prefix — trust it
        if (str_starts_with($digits, '+')) {
            return $digits;
        }

        // Strip any leading 1 (US country code written without +)
        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            return '+' . $digits;
        }

        // Assume US/CA (+1)
        return '+1' . $digits;
    }
}
