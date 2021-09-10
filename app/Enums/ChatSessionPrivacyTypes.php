<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ChatSessionPrivacyTypes extends Enum
{
    const Public =      1;
    const Private =     2;
    const Admin =       3;
}
