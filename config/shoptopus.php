<?php

use App\Enums\UserRole;
use App\Notifications\NewOrder;
use App\Notifications\ProductOutOfStock;
use App\Notifications\ProductRunningLow;
use App\Notifications\UserSignup;

return [
    'user_avatar_dimensions' => [400, 400],
    'menu_image_dimensions' => [200, 200],
    'header_image_dimensions' => [2000, 1200],
    'product_image_dimensions' => [1024, 768],
    'payment_proof_image_dimensions' => [1024, 768],
    'banner_image_dimensions' => [2048, 768],

    'discount_rules' => [
        'allow_discount_stacking' => false,
        'applied_discount' => 'highest', // either highest or lowest
        'voucher_code_basis' => 'final_price', // final_price or total_price
        // When it is set to final_price, the basis of the price the voucher code discount is applied to will be the already discounted price.
        // When it is set to full_price, the basis of the price the voucher code discount is applied to will be the products full price.
    ],
    'modules' => [
        'ratings' => true,
        'marketplace' => true,
        'payments' => true,
    ],
    'super_user' => [
        'name' => env('SUPER_USER_NAME', 'Super User'),
        'email' => env('SUPER_USER_EMAIL', 'superuser@email.com'),
    ],
    'notifications' => [
        ProductOutOfStock::class => [
            UserRole::StoreManager,
            UserRole::StoreAssistant,
            UserRole::SuperAdmin,
            UserRole::Admin,
        ],
        ProductRunningLow::class => [
            UserRole::StoreManager,
            UserRole::StoreAssistant,
            UserRole::SuperAdmin,
            UserRole::Admin,
        ],
        UserSignup::class => [
            UserRole::StoreManager,
            UserRole::SuperAdmin,
            UserRole::Admin,
        ],
        NewOrder::class => [
            UserRole::StoreManager,
            UserRole::StoreAssistant,
            UserRole::SuperAdmin,
        ],
    ],
];
