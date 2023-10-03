<?php

namespace App\Repositories\Admin\DiscountRule;

use App\Models\DiscountRule;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface DiscountRuleRepositoryInterface
{
    public function bulkExpire(array $ids): bool;

    public function bulkStart(array $ids): bool;

    public function bulkActivateForPeriod(array $ids, int $type): bool;

    public function bulkDelete(array $ids): bool;

    public function getAvailableCategories(DiscountRule $discountRule): AnonymousResourceCollection;

    public function getAvailableProducts(DiscountRule $discountRule): AnonymousResourceCollection;

}
