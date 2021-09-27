<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ProductAttributeTypes extends Enum
{
    const Text =        0;
    const Image =       1;
    const Color =       2;
}
