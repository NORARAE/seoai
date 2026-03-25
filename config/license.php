<?php

return [
    'plans' => [
        'starter' => [
            'monthly_price' => 29700,
            'urls_allowed' => 500,
            'stripe_price_id' => env('STRIPE_STARTER_PRICE_ID'),
        ],
        'growth' => [
            'monthly_price' => 59700,
            'urls_allowed' => 2500,
            'stripe_price_id' => env('STRIPE_GROWTH_PRICE_ID'),
        ],
        'scale' => [
            'monthly_price' => 149700,
            'urls_allowed' => 10000,
            'stripe_price_id' => env('STRIPE_SCALE_PRICE_ID'),
        ],
        'agency' => [
            'monthly_price' => 299700,
            'urls_allowed' => null,
            'stripe_price_id' => env('STRIPE_AGENCY_PRICE_ID'),
        ],
    ],
];