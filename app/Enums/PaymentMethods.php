<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class PaymentMethods extends Enum
{
    const Stripe =      0;
    const PayPal =      1;
    const GooglePay =   2;
    const ApplePay =    3;
}
