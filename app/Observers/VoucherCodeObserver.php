<?php

namespace App\Observers;

use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Models\VoucherCode;

class VoucherCodeObserver
{
    public function creating(VoucherCode $voucherCode)
    {
        do {
            $code = GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndNumbers);
        } while (VoucherCode::where('code', $code)->first());
        $voucherCode->code = $code;
    }
}
