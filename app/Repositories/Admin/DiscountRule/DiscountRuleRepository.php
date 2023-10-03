<?php

namespace App\Repositories\Admin\DiscountRule;

use App\Exceptions\InvalidTimePeriodException;
use App\Http\Resources\Admin\ProductCategorySelectResource;
use App\Http\Resources\Admin\ProductSelectResource;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Traits\TimeperiodHelperTrait;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DiscountRuleRepository implements DiscountRuleRepositoryInterface
{
    use TimeperiodHelperTrait;

    public function bulkExpire(array $ids): bool
    {
        try {
            DB::table('discount_rules')->whereIn('id', $ids)->update(['valid_until' => Carbon::now()]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function bulkStart(array $ids): bool
    {
        try {
            DB::table('discount_rules')->whereIn('id', $ids)->update(['valid_from' => Carbon::now()]);

            return true;
        } catch (\Exception $exception) {
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
            DB::table('discount_rules')->whereIn('id', $ids)->update([
                'valid_from' => Carbon::now(),
                'valid_until' => $expiry,
            ]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function bulkDelete(array $ids): bool
    {
        try {
            DB::table('discount_rules')->whereIn('id', $ids)->update(['deleted_at' => Carbon::now()]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getAvailableCategories(DiscountRule $discountRule): AnonymousResourceCollection
    {
        $alreadyAssociatedCategoryIds = $discountRule->categories->pluck('id')->toArray();
        $availableProductCategories = ProductCategory::whereNotIn('id', $alreadyAssociatedCategoryIds)->get();
        return ProductCategorySelectResource::collection($availableProductCategories);
    }

    public function getAvailableProducts(DiscountRule $discountRule): AnonymousResourceCollection
    {
        $alreadyAssociatedProductIds = $discountRule->products->pluck('id')->toArray();
        $availableProducts = Product::whereNotIn('id', $alreadyAssociatedProductIds)->get();
        return ProductSelectResource::collection($availableProducts);
    }
}
