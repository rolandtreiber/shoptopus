<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ProductStatuses extends Enum
{
    const Provisional =   0;
    const Active =        1;
    const Discontinued =  2;
}
