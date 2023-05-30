<?php

namespace App\Repositories\Admin\Payment;

use Illuminate\Support\Facades\DB;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function bulkUpdateStatus(array $ids, int $status): bool
    {
        try {
            DB::table('payments')->whereIn('id', $ids)->update(['status' => $status]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
