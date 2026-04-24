<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Anti-Spam System
    |--------------------------------------------------------------------------
    |
    | Configuration for the InquiryAntiSpamService hard-rule layer.
    | This runs before enrichment and persistence to catch obvious spam early.
    |
    */

    'enabled' => env('ANTISPAM_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | IP Blocklist
    |--------------------------------------------------------------------------
    | IPs that are always blocked, regardless of other signals.
    | Add confirmed spam IPs here. Supports exact IPv4 and IPv6 addresses.
    |
    */
    'block_ips' => [
        '80.94.95.202',
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocked Company Names
    |--------------------------------------------------------------------------
    | Company field values that are obviously fake or spam bait.
    | Matched case-insensitively against the full company string.
    |
    */
    'blocked_companies' => [
        'google',
        'test',
        'admin',
        'seo',
        'facebook',
        'microsoft',
        'amazon',
        'apple',
        'asdf',
        'qwerty',
        'spam',
    ],

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA
    |--------------------------------------------------------------------------
    | Submissions with a reCAPTCHA v3 score below this threshold are blocked.
    | Only applied when a score is present (null scores are not blocked here —
    | they are handled by the enrichment risk scorer).
    |
    */
    'recaptcha_min_score' => env('RECAPTCHA_MIN_SCORE', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Minimum Submission Time
    |--------------------------------------------------------------------------
    | Reject submissions faster than this many seconds (bot behaviour).
    | Set to 0 to disable this check.
    |
    */
    'min_submit_seconds' => env('ANTISPAM_MIN_SUBMIT_SECONDS', 3),

    /*
    |--------------------------------------------------------------------------
    | VPN / Proxy Blocking
    |--------------------------------------------------------------------------
    | When true, submissions from detected VPN/proxy IPs are blocked unless
    | a high-trust signal is present (e.g. business email + valid website).
    |
    */
    'block_vpn_proxy' => env('ANTISPAM_BLOCK_VPN_PROXY', true),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    | Maximum number of submissions from the same IP within the window.
    |
    */
    'rate_limit_attempts' => env('ANTISPAM_RATE_LIMIT_ATTEMPTS', 5),
    'rate_limit_minutes' => env('ANTISPAM_RATE_LIMIT_MINUTES', 10),

    /*
    |--------------------------------------------------------------------------
    | Duplicate Suppression Window
    |--------------------------------------------------------------------------
    | How many minutes to suppress re-submission of the same email or message.
    |
    */
    'duplicate_window_minutes' => env('ANTISPAM_DUPLICATE_WINDOW_MINUTES', 10),

];
