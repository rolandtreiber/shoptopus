<?php

namespace App\Repositories\Admin\VoucherCode;

interface VoucherCodeRepositoryInterface
{
    public function bulkExpire(array $ids): bool;

    public function bulkStart(array $ids): bool;

    public function bulkActivateForPeriod(array $ids, int $type): bool;

    public function bulkDelete(array $ids): bool;
}
