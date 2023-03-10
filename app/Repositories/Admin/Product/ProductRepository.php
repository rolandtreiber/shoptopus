<?php

namespace App\Repositories\Admin\Product;

use App\Enums\ProductStatus;
use App\Exceptions\NotificationException;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ProductOutOfStock;
use App\Notifications\ProductRunningLow;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @param  array  $ids
     * @return bool
     */
    public function bulkDelete(array $ids): bool
    {
        try {
            DB::table('products')->whereIn('id', $ids)->update(['deleted_at' => Carbon::now()]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param  array  $ids
     * @return bool
     */
    public function bulkArchive(array $ids): bool
    {
        try {
            DB::table('products')->whereIn('id', $ids)->update(['status' => ProductStatus::Discontinued]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param  Product  $product
     * @return bool
     *
     * @throws NotificationException
     */
    public function triggerRunningLowNotification(Product $product): bool
    {
        $result = true;
        $notificationsConfig = config('shoptopus.notifications');
        if (array_key_exists(ProductRunningLow::class, $notificationsConfig)) {
            try {
                $users = User::role($notificationsConfig[ProductRunningLow::class])->get();
                foreach ($users as $user) {
                    try {
                        $user->notify(new ProductRunningLow($product, $user->id));
                    } catch (\Exception $exception) {
                        // Notifications should fail silently but logged
                        Log::error($exception->getMessage());
                        $result = false;
                    }
                }
            } catch (RoleDoesNotExist $exception) {
                Log::error($exception->getMessage());
            }
        }

        return $result;
    }

    /**
     * @param  Product  $product
     * @return bool
     */
    public function triggerOutOfStockNotification(Product $product): bool
    {
        $result = true;
        $notificationsConfig = config('shoptopus.notifications');
        if (array_key_exists(ProductOutOfStock::class, $notificationsConfig)) {
            try {
                $users = User::role($notificationsConfig[ProductOutOfStock::class])->get();
                foreach ($users as $user) {
                    try {
                        $user->notify(new ProductOutOfStock($product, $user->id));
                    } catch (\Exception $exception) {
                        // Notifications should fail silently but logged
                        Log::error($exception->getMessage());
                        $result = false;
                    }
                }
            } catch (RoleDoesNotExist $exception) {
                Log::error($exception->getMessage());
            }
        }

        return $result;
    }
}
