<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS / Twilio Settings
    |--------------------------------------------------------------------------
    */

    // Master switch — set to false to disable all SMS sending globally.
    'enabled' => env('SMS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Reminder Windows
    |--------------------------------------------------------------------------
    |
    | Each reminder window specifies how many minutes before the appointment
    | the reminder should fire. The cron command polls every 15 minutes, so
    | windows should be at least 15 minutes wide.
    |
    | 24h reminder: dispatched during the 09:00–09:15 AM PT window the day before.
    | 2h reminder : dispatched when the appointment is 115–130 minutes away.
    |
    */

    'reminder_windows' => [
        '24h' => [
            'dispatch_hour_pt' => env('SMS_24H_DISPATCH_HOUR', 9),   // 9 AM PT
            'window_minutes' => 15,
        ],
        '2h' => [
            'minutes_before_start' => 115,
            'window_minutes' => 15,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio (credentials live in config/services.php)
    |--------------------------------------------------------------------------
    */

    'provider' => env('SMS_PROVIDER', 'twilio'),

];
