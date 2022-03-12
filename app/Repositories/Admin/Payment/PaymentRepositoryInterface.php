<?php

namespace App\Repositories\Admin\Payment;

interface PaymentRepositoryInterface {

    public function bulkUpdateStatus(array $ids, int $status): bool;

}
