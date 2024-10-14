<?php

namespace PublicApi\Checkout;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group guest-checkout-revert-pending-order
 */
class GuestCheckoutRevertPendingOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
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

        $productVariant1 = ProductVariant::factory()->state([
            'stock' => 50
        ])->create();
        $productVariant2 = ProductVariant::factory()->state([
            'stock' => 50,
            'product_id' => $productVariant1->product_id
        ])->create();
        $order->products()->attach($productVariant1->product_id, [
            'amount' => 7,
            'product_variant_id' => $productVariant1->id
        ]);

        $res = $this->sendRequest([
            "user_id" => $user->id,
            "order_id" => $order->id
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $products[0]->id,
            'stock' => 104
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $products[1]->id,
            'stock' => 104
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $productVariant1->id,
            'product_id' => $productVariant1->product_id,
            'stock' => 57
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $productVariant2->id,
            'product_id' => $productVariant1->product_id,
            'stock' => 50
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $productVariant1->product_id,
            'stock' => 107
        ]);

        $res->assertOk();
    }

    /**
     * @test
     */
    public function guest_checkout_revert_pending_order_puts_products_back_into_cart()
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

        $productVariant1 = ProductVariant::factory()->state([
            'stock' => 50
        ])->create();
        $order->products()->attach($productVariant1->product_id, [
            'amount' => 7,
            'product_variant_id' => $productVariant1->id
        ]);

        $this->sendRequest([
            "user_id" => $user->id,
            "order_id" => $order->id
        ]);

        $cart = $user->cart;

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $products[0]->id,
            'quantity' => 4
        ]);
    }

    /**
     * @test
     */
    public function guest_checkout_reverting_order_fails_when_user_is_not_temporary()
    {
        $user = User::factory()->state([
            'temporary' => 0
        ])->create();
        $order = Order::factory()->state([
            'user_id' => $user->id,
            'status' => OrderStatus::AwaitingPayment
        ])->create();
        $products = Product::factory()->state(['stock' => 100])->count(2)->create();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['amount' => 4]);
        }

        $res = $this->sendRequest([
            "user_id" => $user->id,
            "order_id" => $order->id
        ]);

        $res->assertForbidden();
    }

    /**
     * @test
     */
    public function guest_checkout_reverting_order_fails_when_order_does_not_belong_to_user()
    {
        $users = User::factory()->state([
            'temporary' => 1
        ])->count(2)->create();
        $order = Order::factory()->state([
            'user_id' => $users[0]->id,
            'status' => OrderStatus::AwaitingPayment
        ])->create();
        $products = Product::factory()->state(['stock' => 100])->count(2)->create();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['amount' => 4]);
        }

        $res = $this->sendRequest([
            "user_id" => $users[1]->id,
            "order_id" => $order->id
        ]);

        $res->assertForbidden();
    }

    /**
     * @test
     */
    public function guest_checkout_reverting_order_fails_when_order_status_is_not_pending()
    {
        $user = User::factory()->state([
            'temporary' => 1
        ])->create();
        $order = Order::factory()->state([
            'user_id' => $user->id,
            'status' => OrderStatus::PaymentFailed
        ])->create();
        $products = Product::factory()->state(['stock' => 100])->count(2)->create();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['amount' => 4]);
        }

        $res = $this->sendRequest([
            "user_id" => $user->id,
            "order_id" => $order->id
        ]);

        $res->assertForbidden();
    }

    protected function sendRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.revert.order', $data));
    }

}
