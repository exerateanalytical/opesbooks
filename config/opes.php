<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cameroonian Tax Constants
    |--------------------------------------------------------------------------
    */
    'tva_rate'  => env('OPES_TVA_RATE', 0.175),   // 17.5%
    'cac_rate'  => env('OPES_CAC_RATE', 0.10),    // 10% of TVA
    'smig_xaf'  => env('OPES_SMIG_XAF', 36270),   // Salaire Minimum Interprofessionnel Garanti

    /*
    |--------------------------------------------------------------------------
    | Subscription Plan Prices (XAF)
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'STARTER'    => env('OPES_PLAN_STARTER',    5000),
        'GROWTH'     => env('OPES_PLAN_GROWTH',     15000),
        'ENTERPRISE' => env('OPES_PLAN_ENTERPRISE', 45000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default GL Accounts
    |--------------------------------------------------------------------------
    */
    'default_caisse_code' => env('OPES_DEFAULT_CAISSE', '571100'),
];
