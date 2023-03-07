<?php

namespace Database\Seeders\TestData;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ProductStatus;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSource;
use App\Models\Product;
use App\Models\User;
use App\Models\VoucherCode;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        $productIds = DB::table('products')->where('status', ProductStatus::Active)->pluck('id')->toArray();

        $ordersToCreate = random_int(10, 15);

        for ($i = 0; $i < $ordersToCreate; $i++) {
            $selectedUser = User::find($userIds[random_int(1, sizeof($userIds)-1)]);
            $selectedUserId = $selectedUser->id;
            $order = new Order();
            $order->user_id = $selectedUserId;
            $order->status =  random_int(1, 6);
            $deliveryTypeIds = DB::table('delivery_types')->where('enabled', 1)->pluck('id')->toArray();
            $order->delivery_type_id = $deliveryTypeIds[random_int(0, sizeof($deliveryTypeIds)-1)];
            $order->created_at = Carbon::now()->subDays(random_int(1, 30));
            $userAddresses = $selectedUser->addresses;
            $selectedAddress = random_int(0, sizeof($userAddresses) - 1);
            $order->address_id = $userAddresses[$selectedAddress]->id;
            $hasVoucherCode = random_int(0, 50) < 15;
            if ($hasVoucherCode) {
                $voucherCodeIds = DB::table('voucher_codes')->where('enabled', 1)->pluck('id')->toArray();
                $order->voucher_code_id = $voucherCodeIds[random_int(0, sizeof($voucherCodeIds)-1)];
            }
            $order->save();

            $usedProductTypes = [];
            $productTypesToAddCount = random_int(1, round($products/2));
            for ($n = 0; $n < $productTypesToAddCount; $n++) {
                do {
                    $selectedProduct = Product::find($productIds[random_int(1, $products-1)]);
                } while (in_array($selectedProduct->id, $usedProductTypes));
                $usedProductTypes[] = $selectedProduct->id;
                $variantIds = DB::table('product_variants')->where('product_id', $selectedProduct->id)->where('enabled', 1)->pluck('id')->toArray();
                if (count($variantIds) > 0) {
                    $selectedProductVariantId = $variantIds[random_int(0, count($variantIds)-1)];
                    $order->products()->attach($selectedProduct->id, ['amount' => random_int(1, 10), 'product_variant_id' => $selectedProductVariantId]);
                } else {
                    $order->products()->attach($selectedProduct->id, ['amount' => random_int(1, 10)]);
                }
            }

            $order = Order::find($order->id);
            $sources = $selectedUser->payment_sources;
            if (sizeof($sources) > 0) {
                $paymentSourceId = $sources[random_int(0, $sources->count()-1)]->id;
            } else {
                $paymentSource = PaymentSource::factory()->state(['user_id' => $selectedUserId])->create();
                $paymentSourceId = $paymentSource->id;
            }

            Payment::factory()->state([
                'amount' => $order->total_price,
                'user_id' => $selectedUser->id,
                'payment_source_id' => $paymentSourceId,
                'payable_type' => Order::class,
                'payable_id' => $order->id,
                'status' => PaymentStatus::Settled,
                'type' => PaymentType::Payment
            ])->create();
        }
    }
}
