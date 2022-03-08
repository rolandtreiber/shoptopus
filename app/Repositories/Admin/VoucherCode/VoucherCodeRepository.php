<?php

namespace App\Repositories\Admin\VoucherCode;

use App\Exceptions\InvalidTimePeriodException;
use App\Traits\TimeperiodHelperTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VoucherCodeRepository implements VoucherCodeRepositoryInterface
{
    use TimeperiodHelperTrait;

    public function bulkExpire(array $ids): bool
    {
        try {
            DB::table('voucher_codes')->whereIn('id', $ids)->update(['valid_until' => Carbon::now()]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }

    public function bulkStart(array $ids): bool
    {
        try {
            DB::table('voucher_codes')->whereIn('id', $ids)->update(['valid_from' => Carbon::now()]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }

    /**
     * @throws InvalidTimePeriodException
     */
    public function bulkActivateForPeriod(array $ids, int $type): bool
    {
        $expiry = $this->getDateFromType($type);

        try {
            DB::table('voucher_codes')->whereIn('id', $ids)->update([
                'valid_from' => Carbon::now(),
                'valid_until' => $expiry
            ]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }

    public function bulkDelete(array $ids): bool
    {
        try {
            DB::table('voucher_codes')->whereIn('id', $ids)->update(['deleted_at' => Carbon::now()]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }
}
