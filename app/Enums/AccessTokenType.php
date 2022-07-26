<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class AccessTokenType
 * @package App\Enums
 */
final class AccessTokenType extends Enum
{
    public const General = 0;
    public const Invoice = 1;
}
