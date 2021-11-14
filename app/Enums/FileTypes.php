<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FileTypes extends Enum
{
    const Image =           0;
    const Video =           1;
    const Audio =           2;
    const Pdf =             3;
    const Spreadsheet =     4;
    const TextDocument =    5;
    const Other =           6;
}
