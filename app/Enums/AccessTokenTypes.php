<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class AccessTokenTypes
 * @package App\Enums
 */
final class AccessTokenTypes extends Enum
{
    public const General =                 0;
    public const PasswordReset =           1;
    public const EmailConfirmation =       2;
}
