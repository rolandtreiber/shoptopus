<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ProductAttributeType extends Enum
{
    const Text =        1;
    const Image =       2;
    const Color =       3;
}
