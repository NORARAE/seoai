<?php

namespace App\Support;

class EmailUrl
{
    /**
     * Generate a tracked email CTA URL.
     *
     * @param string      $destination Relative path (e.g. '/dashboard')
     * @param string      $emailType   Email identifier (e.g. 'upgrade-analysis-complete')
     * @param int|null    $userId
     * @param int|null    $scanId
     */
    public static function tracked(string $destination, string $emailType, ?int $userId = null, ?int $scanId = null): string
    {
        $params = [
            'd' => $destination,
            't' => $emailType,
        ];

        if ($userId) {
            $params['u'] = $userId;
        }

        if ($scanId) {
            $params['s'] = $scanId;
        }

        return url('/email/click?' . http_build_query($params));
    }
}
