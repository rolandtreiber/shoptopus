<?php

namespace App\Repositories\Admin\Order;

interface OrderRepositoryInterface {

    public function bulkUpdateStatus(array $ids, int $status): bool;

}
