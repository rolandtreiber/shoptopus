<?php

namespace App\Repositories\Admin\Order;

use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{

    public function bulkUpdateStatus(array $ids, int $status): bool
    {
        try {
            DB::table('orders')->whereIn('id', $ids)->update(['status' => $status]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }
}
