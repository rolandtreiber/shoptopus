<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderStatus extends Enum
{
    const AwaitingPayment = 1;

    const Paid = 2;

    const Processing = 3;

    const InTransit = 4;

    const Completed = 5;

    const OnHold = 6;

    const Cancelled = 7;
    const PaymentFailed = 8;

}
