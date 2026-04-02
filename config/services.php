<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        // Service account credentials (JSON file path). OAuth keys below are only needed for GSC.
        'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS', storage_path('google-credentials.json')),
        'calendar_id' => env('GOOGLE_CALENDAR_ID'),
        'calendar_enabled' => env('GOOGLE_CALENDAR_ENABLED', false),
        // GSC OAuth (not used by Calendar)
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/admin/gsc/callback'),
        // Socialite callback for Google login (separate from GSC redirect_uri)
        'redirect' => env('GOOGLE_LOGIN_REDIRECT', env('APP_URL') . '/auth/google/callback'),
    ],

    'registration' => [
        'access_code' => env('REGISTRATION_ACCESS_CODE'),
    ],

    'google_login' => [
        'enabled' => env('GOOGLE_LOGIN_ENABLED', true),
        'auto_provision' => env('GOOGLE_AUTO_PROVISION_USERS', false),
        'allowed_domains' => env('GOOGLE_ALLOWED_DOMAINS', ''),
        'default_role' => env('GOOGLE_DEFAULT_ROLE', 'viewer'),
    ],

    'booking' => [
        'owner_email' => env('BOOKING_OWNER_EMAIL', 'hello@seoaico.com'),
        'owner_name' => env('BOOKING_OWNER_NAME', 'SEOAIco'),
    ],

    'inquiry' => [
        'recipient_email' => env('ADMIN_NOTIFICATION_EMAIL', env('BOOKING_OWNER_EMAIL', 'hello@seoaico.com')),
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'coinbase_commerce' => [
        'enabled' => env('COINBASE_COMMERCE_ENABLED', false),
        'api_key' => env('COINBASE_COMMERCE_API_KEY'),
        'webhook_secret' => env('COINBASE_COMMERCE_WEBHOOK_SECRET'),
    ],

    'gsc' => [
        'site_url' => env('GSC_SITE_URL'),
        'credentials_path' => env('GOOGLE_APPLICATION_CREDENTIALS', storage_path('app/google-credentials.json')),
    ],

    'ga4' => [
        'property_id' => env('GA4_PROPERTY_ID'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_FROM_NUMBER'),
    ],

    // Stripe webhook secret for the booking payment flow (separate from license webhook).
    'stripe_booking' => [
        'webhook_secret' => env('STRIPE_BOOKING_WEBHOOK_SECRET'),
    ],

    'clarity' => [
        'project_id' => env('CLARITY_PROJECT_ID', ''),
    ],

];
