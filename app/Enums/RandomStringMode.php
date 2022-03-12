<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class RandomStringMode
 * @package App\Enums
 */
final class RandomStringMode extends Enum
{
    public const UppercaseLowercaseAndNumbers =   0;
    public const UppercaseAndNumbers =            1;
    public const LowecaseAndNumbers =             2;
    public const UppercaseAndLowecase =           3;
    public const NumbersOnly =                    4;
}
