<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentMethod extends Enum
{
    const Stripe =      1;
    const PayPal =      2;
    const GooglePay =   3;
    const ApplePay =    4;
    const AmazonPay =   5;
}
