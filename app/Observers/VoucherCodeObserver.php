<?php

namespace App\Observers;

use App\Enums\RandomStringModes;
use App\Helpers\GeneralHelper;
use App\Models\VoucherCode;

class VoucherCodeObserver
{
    /**
     * @param VoucherCode $voucherCode
     */
    public function creating(VoucherCode $voucherCode)
    {
        do {
            $code = GeneralHelper::generateRandomString(10, RandomStringModes::UppercaseAndNumbers);
        } while (VoucherCode::where('code', $code)->first());
        $voucherCode->code = $code;
    }
}
