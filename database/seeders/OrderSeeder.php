<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VoucherCode;
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
        $userIds = User::role('customer')->pluck('id');
        $products = Product::count();

        $ordersToCreate = random_int(1, 20);

        for ($i = 0; $i < $ordersToCreate; $i++) {
            $selectedUser = User::find($userIds[random_int(1, sizeof($userIds)-1)]);
            $selectedUserId = $selectedUser->id;
            $order = new Order();
            $order->user_id = $selectedUserId;
            $order->delivery_type_id = (new DeliveryType)->findNthId(random_int(1, DeliveryType::count()));
            $userAddresses = $selectedUser->addresses;
            $selectedAddress = random_int(0, sizeof($userAddresses) - 1);
            $order->address_id = $userAddresses[$selectedAddress]->id;
            $hasVoucherCode = random_int(0, 50) < 15;
            if ($hasVoucherCode) {
                $voucherCodeIds = VoucherCode::all()->pluck('id');
                $order->voucher_code_id = $voucherCodeIds[random_int(0, sizeof($voucherCodeIds)-1)];
            }
            $order->save();

            $usedProductTypes = [];
            $productTypesToAddCount = random_int(1, $products);
            for ($n = 0; $n < $productTypesToAddCount; $n++) {
                do {
                    $selectedProductId = (new Product)->findNthId(random_int(1, $products));
                } while (in_array($selectedProductId, $usedProductTypes));
                $usedProductTypes[] = $selectedProductId;
                $order->products()->attach($selectedProductId, ['amount' => random_int(1, 10)]);
            }
        }
    }
}
