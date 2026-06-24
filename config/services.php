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

    // ── DGI Fiscalis / SIGIT (Cameroun e-invoicing) ──────────────────────────
    'dgi' => [
        'endpoint'    => env('DGI_ENDPOINT', 'https://teledeclaration-dgi.cm/api/invoices'),
        'api_key'     => env('DGI_API_KEY'),
        'timeout'     => env('DGI_TIMEOUT', 15),
    ],

    // ── Mobile Money payment aggregator (Maviance / Bizao / CinetPay) ─────────
    'payment_aggregator' => [
        'endpoint'    => env('PAYMENT_AGGREGATOR_ENDPOINT'),
        'api_key'     => env('PAYMENT_AGGREGATOR_API_KEY'),
        'merchant_id' => env('PAYMENT_AGGREGATOR_MERCHANT_ID'),
    ],

    // ── MTN Mobile Money (direct integration fallback) ────────────────────────
    'mtn_momo' => [
        'base_url'        => env('MTN_MOMO_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
        'subscription_key'=> env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'api_user'        => env('MTN_MOMO_API_USER'),
        'api_key'         => env('MTN_MOMO_API_KEY'),
        'environment'     => env('MTN_MOMO_ENVIRONMENT', 'sandbox'),
    ],

    // ── Orange Money (direct integration fallback) ───────────────────────────
    'orange_money' => [
        'base_url'    => env('ORANGE_MONEY_BASE_URL', 'https://api.orange.com/orange-money-webpay/cm/v1'),
        'client_id'   => env('ORANGE_MONEY_CLIENT_ID'),
        'client_secret'=> env('ORANGE_MONEY_CLIENT_SECRET'),
        'merchant_key'=> env('ORANGE_MONEY_MERCHANT_KEY'),
    ],

];
