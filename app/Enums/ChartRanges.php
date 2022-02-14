<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ChartRanges extends Enum
{
    const LastWeek =    1;
    const LastMonth =   2;
    const LastYear =    3;
}
