<?php

return [
    // sandbox | live — sandbox auto-confirms payments for testing.
    'environment' => env('PAYMENT_ENV', 'sandbox'),

    'orange_money' => [
        'base_url'        => env('ORANGE_MONEY_URL', 'https://api.orange.com/orange-money-webpay/cm/v1'),
        'merchant_key'    => env('ORANGE_MONEY_MERCHANT_KEY'),
        'merchant_secret' => env('ORANGE_MONEY_MERCHANT_SECRET'),
    ],

    'mtn_momo' => [
        'base_url'         => env('MTN_MOMO_URL', 'https://sandbox.momodeveloper.mtn.com'),
        'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'api_user'         => env('MTN_MOMO_API_USER'),
        'api_key'          => env('MTN_MOMO_API_KEY'),
        'environment'      => env('MTN_MOMO_TARGET_ENV', 'sandbox'),
    ],
];
