<?php

namespace App\Enums;

use App\Notifications\NewOrder;
use App\Notifications\ProductOutOfStock;
use App\Notifications\ProductRunningLow;
use App\Notifications\UserSignup;
use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class NotificationType extends Enum
{
    const NewOrderPlaced = [
        'type' => 'new-order',
        'className' => NewOrder::class,
    ];

    const ProductOutOfStock = [
        'type' => 'product-out-of-stock',
        'className' => ProductOutOfStock::class,
    ];

    const ProductRunningLow = [
        'type' => 'product-running-low',
        'className' => ProductRunningLow::class,
    ];

    const UserSignup = [
        'type' => 'user-signup',
        'className' => UserSignup::class,
    ];
}
