<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProductStatus extends Enum
{
    const Provisional =   1;
    const Active =        2;
    const Discontinued =  3;
}
