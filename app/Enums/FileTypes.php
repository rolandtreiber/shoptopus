<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class FileTypes extends Enum
{
    const Image =           1;
    const Video =           2;
    const Audio =           3;
    const Pdf =             4;
    const Spreadsheet =     5;
    const TextDocument =    6;
    const Other =           7;
}
