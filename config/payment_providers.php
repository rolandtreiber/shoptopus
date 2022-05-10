<?php

return [
    'providers' => [
        'paypal',
//        'amazon',
        'stripe',
    ],
    'provider_settings' => [
        'paypal' => [
            'live' => [
                'ACCOUNT' => env('PAYPAL_LIVE_ACCOUNT', 'please fill me in'),
                'CLIENT_ID' => env('PAYPAL_LIVE_CLIENT_ID', 'please fill me in'),
                'SECRET' => env('PAYPAL_LIVE_SECRET', 'please fill me in')
            ],
            'sandbox' => [
                'ACCOUNT' => env('PAYPAL_SANDBOX_ACCOUNT', 'please fill me in'),
                'CLIENT_ID' => env('PAYPAL_SANDBOX_CLIENT_ID', 'please fill me in'),
                'SECRET' => env('PAYPAL_SANDBOX_SECRET', 'please fill me in')
            ]
        ],
        'stripe' => [
            'live' => [
                'publishable_key' => env('STRIPE_KEY', 'please fill me in'),
                'secret_key' => env('STRIPE_SECRET_KEY', 'please fill me in')
            ],
            'sandbox' => [
                'publishable_key' => env('STRIPE_SANDBOX_KEY'),
                'secret_key' => env('STRIPE_SANDBOX_SECRET_KEY')
            ]
        ]
    ]
];
