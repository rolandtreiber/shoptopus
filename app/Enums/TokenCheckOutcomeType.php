<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class TokenCheckOutcomeType
 * @package App\Enums
 */
final class TokenCheckOutcomeType extends Enum
{
    public const Success =      0;
    public const TokenExpired = 1;
    public const TokenInvalid = 2;
    public const UserNotFound = 3;
}
