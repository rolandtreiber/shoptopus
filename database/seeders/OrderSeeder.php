<?php

namespace Database\Seeders;

use App\Enums\OrderStatuses;
use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSource;
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

        $ordersToCreate = random_int(10, 50);

        for ($i = 0; $i < $ordersToCreate; $i++) {
            /** @var User $selectedUser */
            $selectedUser = User::find($userIds[random_int(1, sizeof($userIds)-1)]);
            $selectedUserId = $selectedUser->id;
            $order = new Order();
            $order->user_id = $selectedUserId;
            $order->status = OrderStatuses::Paid;
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
                    $selectedProduct = (new Product)->findNth(random_int(1, $products));
                } while (in_array($selectedProduct->id, $usedProductTypes));
                $usedProductTypes[] = $selectedProduct->id;
                $variants = $selectedProduct->productVariants;
                if (count($variants) > 0) {
                    $selectedProductVariant = $variants[random_int(0, count($variants)-1)];
                    $order->products()->attach($selectedProduct->id, ['amount' => random_int(1, 10), 'product_variant_id' => $selectedProductVariant->id]);
                } else {
                    $order->products()->attach($selectedProduct->id, ['amount' => random_int(1, 10)]);
                }
            }

            $order = Order::find($order->id);
            $sources = $selectedUser->paymentSources;
            if (sizeof($sources) > 0) {
                $paymentSourceId = $sources[random_int(0, $selectedUser->paymentSources()->count()-1)]->id;
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
                'status' => PaymentStatuses::Settled,
                'type' => PaymentTypes::Payment
            ])->create();
        }
    }
}
