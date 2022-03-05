<?php

namespace App\Repositories\Admin\Product;

use App\Enums\ProductStatuses;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{

    public function bulkDelete(array $ids): bool
    {
        try {
            DB::table('products')->whereIn('id', $ids)->update(['deleted_at' => Carbon::now()]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }

    public function bulkArchive(array $ids): bool
    {
        try {
            DB::table('products')->whereIn('id', $ids)->update(['status' => ProductStatuses::Discontinued]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }
}
