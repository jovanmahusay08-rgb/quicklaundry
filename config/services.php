<?php

return [
    'semaphore' => [
        'api_key' => env('SEMAPHORE_API_KEY'),
        'sender_name' => env('SEMAPHORE_SENDER_NAME'),
    ],
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'mail' => [
        'transport' => env('MAIL_MAILER', 'smtp'),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'gcash' => [
        'account_name' => env('GCASH_ACCOUNT_NAME'),
        'number' => env('GCASH_NUMBER'),
        'qr_image' => env('GCASH_QR_IMAGE', 'images/payments/gcash-receiver-qr.jpg'),
    ],

    'philsms' => [
        'api_token' => env('PHILSMS_API_TOKEN'),
        'sender_id' => env('PHILSMS_SENDER_ID', 'PhilSMS'),
        'api_url' => env('PHILSMS_API_URL', 'https://dashboard.philsms.com/api/v3/sms/send'),
    ],
];

