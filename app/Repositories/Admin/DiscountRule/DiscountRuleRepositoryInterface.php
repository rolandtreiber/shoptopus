<?php

namespace App\Repositories\Admin\DiscountRule;

interface DiscountRuleRepositoryInterface
{
    public function bulkExpire(array $ids): bool;

    public function bulkStart(array $ids): bool;

    public function bulkActivateForPeriod(array $ids, int $type): bool;

    public function bulkDelete(array $ids): bool;
}
