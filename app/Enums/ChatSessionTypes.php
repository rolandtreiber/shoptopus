<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ChatSessionTypes extends Enum
{
    const ChatRoom =    1;
    const OneToOne =    2;
}
