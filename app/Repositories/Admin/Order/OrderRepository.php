<?php

namespace App\Repositories\Admin\Order;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrder;
use App\Notifications\ProductOutOfStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class OrderRepository implements OrderRepositoryInterface
{
    public function bulkUpdateStatus(array $ids, int $status): bool
    {
        try {
            DB::table('orders')->whereIn('id', $ids)->update(['status' => $status]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function triggerNewOrderNotification(Order $order): bool
    {
        $result = true;
        $notificationsConfig = config('shoptopus.notifications');
        if (array_key_exists(ProductOutOfStock::class, $notificationsConfig)) {
            try {
                $users = User::role($notificationsConfig[NewOrder::class])->get();
                foreach ($users as $user) {
                    try {
                        $user->notify(new NewOrder($order, $user->id));
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
