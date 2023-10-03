<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FileType extends Enum
{
    const Image = 1;

    const Video = 2;

    const Audio = 3;

    const Pdf = 4;

    const Spreadsheet = 5;

    const TextDocument = 6;

    const Other = 7;

    const DownloadOnly = 8;

}
