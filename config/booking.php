<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Booking Reschedule Settings
    |--------------------------------------------------------------------------
    */

    // How many hours before the appointment reschedules are locked out.
    'reschedule_cutoff_hours' => env('BOOKING_RESCHEDULE_CUTOFF_HOURS', 6),

    // Maximum number of times a single booking can be rescheduled.
    'max_reschedules' => env('BOOKING_MAX_RESCHEDULES', 3),

    /*
    |--------------------------------------------------------------------------
    | Owner / Notification Settings
    |--------------------------------------------------------------------------
    */

    'owner_email' => env('BOOKING_OWNER_EMAIL', 'hello@seoaico.com'),
    'owner_name' => env('BOOKING_OWNER_NAME', 'SEOAIco'),

    /*
    |--------------------------------------------------------------------------
    | Slot Duration
    |--------------------------------------------------------------------------
    | Default slot granularity in minutes used when generating available slots
    | without a specific ConsultType duration.
    */

    'default_slot_minutes' => env('BOOKING_DEFAULT_SLOT_MINUTES', 30),

];
