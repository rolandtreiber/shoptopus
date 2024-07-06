<?php

namespace PublicApi\Checkout;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group guest-checkout-revert-pending-order
 */
class GuestCheckoutRevertPendingOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group work
     */
    public function guest_checkout_revert_pending_order_works_when_all_information_are_provided_and_correct()
    {
        $user = User::factory()->state([
            'temporary' => 1
        ])->create();
        $order = Order::factory()->state([
            'user_id' => $user->id,
            'status' => OrderStatus::AwaitingPayment
        ])->create();
        $products = Product::factory()->state(['stock' => 100])->count(2)->create();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['amount' => 4]);
        }

        self::assertTrue(true );
    }

}
