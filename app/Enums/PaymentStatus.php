<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class PaymentStatus extends Enum
{
    const Pending =     0;
    const Settled =     1;
    const Refunded =    2;
    const Rejected =    3;
}
