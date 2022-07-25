<?php

namespace App\Repositories\Admin\Order;

use App\Models\Order;

interface OrderRepositoryInterface {

    public function bulkUpdateStatus(array $ids, int $status): bool;
    public function triggerNewOrderNotification(Order $order): bool;

}
