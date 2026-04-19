<?php

namespace App\Support;

use App\Models\QuickScan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuickScanReportToken
{
    /**
     * Build a time-bound guest report token for a quick scan.
     */
    public static function generate(QuickScan $scan, ?Carbon $expiresAt = null): string
    {
        $expiresAt = $expiresAt ?: now()->addHours(24);

        $payload = [
            'scan_id' => (int) $scan->id,
            'exp' => (int) $expiresAt->timestamp,
            'nonce' => Str::random(10),
        ];

        $encoded = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signature = self::signature($encoded);

        return $encoded . '.' . $signature;
    }

    /**
     * Validate a guest report token for a specific scan.
     */
    public static function isValid(string $token, QuickScan $scan): bool
    {
        if ($token === '' || !str_contains($token, '.')) {
            return false;
        }

        [$encoded, $providedSignature] = explode('.', $token, 2);

        if ($encoded === '' || $providedSignature === '') {
            return false;
        }

        $expected = self::signature($encoded);

        if (!hash_equals($expected, $providedSignature)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($encoded), true);

        if (!is_array($payload)) {
            return false;
        }

        $scanId = (int) ($payload['scan_id'] ?? 0);
        $exp = (int) ($payload['exp'] ?? 0);

        if ($scanId !== (int) $scan->id) {
            return false;
        }

        if ($exp < now()->timestamp) {
            return false;
        }

        return true;
    }

    private static function signature(string $encodedPayload): string
    {
        $key = (string) config('app.key', '');

        return hash_hmac('sha256', $encodedPayload, $key);
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string
    {
        $padding = 4 - (strlen($value) % 4);

        if ($padding < 4) {
            $value .= str_repeat('=', $padding);
        }

        return base64_decode(strtr($value, '-_', '+/')) ?: '';
    }
}
