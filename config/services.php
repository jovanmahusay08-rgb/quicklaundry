<?php

return [
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
];
