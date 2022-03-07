<?php

namespace App\Repositories\Admin\ProductTag;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductTagRepository implements ProductTagRepositoryInterface
{

    public function bulkUpdateAvailability(array $ids, bool $availability): bool
    {
        try {
            DB::table('product_tags')->whereIn('id', $ids)->update(['enabled' => $availability]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }

    public function bulkDelete(array $ids): bool
    {
        try {
            DB::table('product_tags')->whereIn('id', $ids)->update(['deleted_at' => Carbon::now()]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }
}
