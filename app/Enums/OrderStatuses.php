<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderStatuses extends Enum
{
    const Paid =        1;
    const Processing =  2;
    const InTransit =   3;
    const Completed =   4;
    const OnHold =      5;
    const Cancelled =   6;
}
