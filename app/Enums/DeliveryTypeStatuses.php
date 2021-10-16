<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class DeliveryTypeStatuses extends Enum
{
    const Enabled =     1;
    const Disabled =    0;
}
