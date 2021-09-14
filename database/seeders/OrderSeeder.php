<?php

namespace Database\Seeders;

use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $users = User::count();
        $products = Product::count();

        $ordersToCreate = random_int(1, $users);

        for ($i = 0; $i < $ordersToCreate; $i++) {
            $selectedUserId = random_int(1, $ordersToCreate);
            $order = new Order();
            $order->user_id = $selectedUserId;
            $order->delivery_type_id = random_int(1, DeliveryType::count());
            $order->save();

            $usedProductTypes = [];
            $productTypesToAddCount = random_int(1, $products);
            for ($n = 0; $n < $productTypesToAddCount; $n++) {
                do {
                    $selectedProductId = random_int(1, $products);
                } while (in_array($selectedProductId, $usedProductTypes));
                $usedProductTypes[] = $selectedProductId;
                $order->products()->attach($selectedProductId, ['amount' => random_int(1, 10)]);
            }
        }
    }
}
