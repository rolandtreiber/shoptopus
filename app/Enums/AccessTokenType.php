<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class AccessTokenType
 */
final class AccessTokenType extends Enum
{
    public const General = 0;

    public const Invoice = 1;
}
