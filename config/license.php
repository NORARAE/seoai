<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Billing Terms
    |--------------------------------------------------------------------------
    | Minimum term is 3 months. Stripe Price IDs are set per plan × term.
    | 3 months = "tester" entry point.
    */
    'terms' => [
        3  => ['label' => '3 months (Starter)',  'months' => 3],
        6  => ['label' => '6 months',            'months' => 6],
        12 => ['label' => '12 months',           'months' => 12],
    ],

    'min_term_months' => 3,
    'trial_days'      => 30,

    /*
    |--------------------------------------------------------------------------
    | Plans
    |--------------------------------------------------------------------------
    | Each plan has a monthly price and URL cap.
    | stripe_prices maps term (months) → Stripe Price ID.
    */
    'plans' => [
        'starter' => [
            'monthly_price' => 29700,       // $297.00
            'urls_allowed'  => 500,
            'stripe_prices' => [
                3  => env('STRIPE_STARTER_3M_PRICE_ID'),
                6  => env('STRIPE_STARTER_6M_PRICE_ID'),
                12 => env('STRIPE_STARTER_12M_PRICE_ID'),
            ],
        ],
        'growth' => [
            'monthly_price' => 59700,       // $597.00
            'urls_allowed'  => 2500,
            'stripe_prices' => [
                3  => env('STRIPE_GROWTH_3M_PRICE_ID'),
                6  => env('STRIPE_GROWTH_6M_PRICE_ID'),
                12 => env('STRIPE_GROWTH_12M_PRICE_ID'),
            ],
        ],
        'scale' => [
            'monthly_price' => 149700,      // $1,497.00
            'urls_allowed'  => 10000,
            'stripe_prices' => [
                3  => env('STRIPE_SCALE_3M_PRICE_ID'),
                6  => env('STRIPE_SCALE_6M_PRICE_ID'),
                12 => env('STRIPE_SCALE_12M_PRICE_ID'),
            ],
        ],
        'agency' => [
            'monthly_price' => 299700,      // $2,997.00
            'urls_allowed'  => null,         // unlimited
            'stripe_prices' => [
                3  => env('STRIPE_AGENCY_3M_PRICE_ID'),
                6  => env('STRIPE_AGENCY_6M_PRICE_ID'),
                12 => env('STRIPE_AGENCY_12M_PRICE_ID'),
            ],
        ],
    ],
];