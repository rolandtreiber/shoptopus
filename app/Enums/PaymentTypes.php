<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentTypes extends Enum
{
    const Payment =     0;
    const Refund =      1;
}
