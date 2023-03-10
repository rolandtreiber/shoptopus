<?php

return [
    'providers' => [
        'paypal',
        'stripe',
        'amazon',
    ],
    'provider_settings' => [
        'paypal' => [
            'live' => [
                'ACCOUNT' => env('PAYPAL_LIVE_ACCOUNT', 'please fill me in'),
                'CLIENT_ID' => env('PAYPAL_LIVE_CLIENT_ID', 'please fill me in'),
                'SECRET' => env('PAYPAL_LIVE_SECRET', 'please fill me in'),
            ],
            'sandbox' => [
                'ACCOUNT' => env('PAYPAL_SANDBOX_ACCOUNT', 'please fill me in'),
                'CLIENT_ID' => env('PAYPAL_SANDBOX_CLIENT_ID', 'please fill me in'),
                'SECRET' => env('PAYPAL_SANDBOX_SECRET', 'please fill me in'),
            ],
        ],
        'stripe' => [
            'live' => [
                'publishable_key' => env('STRIPE_KEY', 'please fill me in'),
                'secret_key' => env('STRIPE_SECRET_KEY', 'please fill me in'),
            ],
            'sandbox' => [
                'publishable_key' => env('STRIPE_SANDBOX_KEY'),
                'secret_key' => env('STRIPE_SANDBOX_SECRET_KEY'),
            ],
        ],
        'amazon' => [
            'live' => [
                'MERCHANT_ID' => env('AMAZON_LIVE_MERCHANT_ID', 'A2RMNQMF7YBV4O'),
                'STORE_ID' => env('AMAZON_LIVE_STORE_ID', 'amzn1.application-oa2-client.47cf04c2ad834c17a07dd1bf93f89529'),
                'PUBLIC_KEY_ID' => env('AMAZON_LIVE_PUBLIC_KEY_ID', 'SANDBOX-AGV66EJQUQY4HJVL2JQX7CAD'),
                'REGION' => env('AMAZON_LIVE_REGION', 'UK'),
                'STORE_NAME' => env('AMAZON_LIVE_STORE_NAME', 'Winzum'),
            ],
            'sandbox' => [
                'MERCHANT_ID' => env('AMAZON_SANDBOX_MERCHANT_ID', 'A2RMNQMF7YBV4O'),
                'STORE_ID' => env('AMAZON_SANDBOX_STORE_ID', 'amzn1.application-oa2-client.47cf04c2ad834c17a07dd1bf93f89529'),
                'PUBLIC_KEY_ID' => env('AMAZON_SANDBOX_PUBLIC_KEY_ID', 'SANDBOX-AGV66EJQUQY4HJVL2JQX7CAD'),
                'REGION' => env('AMAZON_SANDBOX_REGION', 'UK'),
                'STORE_NAME' => env('AMAZON_SANDBOX_STORE_NAME', 'Winzum'),
            ],
        ],
    ],
];
