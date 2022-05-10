<?php

return [

    'providers' => [
//        'paypal',
//        'amazon',
        'stripe',
    ],
    'provider_settings' => [
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
