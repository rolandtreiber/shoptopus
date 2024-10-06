<?php

namespace PublicApi\Checkout;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group authenticated-checkout-revert-pending-order
 */

class AuthenticatedCheckoutRevertPendingOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    /**
     * @test
     */
    public function authenticated_checkout_revert_pending_order_works_when_all_information_are_provided_and_correct()
    {
        $order = Order::factory()->state([
            'status' => OrderStatus::AwaitingPayment,
            'user_id' => $this->user->id
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

        $res = $this->signIn($this->user)->sendRequest([
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
    public function authenticated_checkout_reverting_order_fails_when_user_is_temporary()
    {
        $this->user->temporary = 1;
        $this->user->save();
        $this->user->refresh();
        $order = Order::factory()->state([
            'user_id' => $this->user->id,
            'status' => OrderStatus::AwaitingPayment
        ])->create();
        $products = Product::factory()->state(['stock' => 100])->count(2)->create();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['amount' => 4]);
        }

        $res = $this->signIn($this->user)->sendRequest([
            "order_id" => $order->id
        ]);

        $res->assertForbidden();
    }

    /**
     * @test
     */
    public function authenticated_checkout_reverting_order_fails_when_order_does_not_belong_to_user()
    {
        $user = User::factory()->create();
        $order = Order::factory()->state([
            'user_id' => $user->id,
            'status' => OrderStatus::AwaitingPayment
        ])->create();
        $products = Product::factory()->state(['stock' => 100])->count(2)->create();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['amount' => 4]);
        }

        $res = $this->signIn($this->user)->sendRequest([
            "order_id" => $order->id
        ]);

        $res->assertForbidden();
    }

    /**
     * @test
     */
    public function guest_checkout_reverting_order_fails_when_order_status_is_not_pending()
    {
        $order = Order::factory()->state([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Cancelled
        ])->create();
        $products = Product::factory()->state(['stock' => 100])->count(2)->create();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['amount' => 4]);
        }

        $res = $this->signIn($this->user)->sendRequest([
            "order_id" => $order->id
        ]);

        $res->assertForbidden();
    }

    /**
     * @test
     */
    public function revert_order_successfully_adds_available_products_to_cart()
    {
        $order = Order::factory()->state([
            'status' => OrderStatus::AwaitingPayment,
            'user_id' => $this->user->id
        ])->create();
        $product = Product::factory()->state(['stock' => 10])->create();
        $order->products()->attach($product->id, ['amount' => 2]);
        $res = $this->signIn($this->user)->sendRequest([
            "order_id" => $order->id
        ]);
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseHas('cart_product', [
            'cart_id' => Cart::where('user_id', $this->user->id)->first()->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $res->assertOk();
    }

    /**
     * @test
     */
    public function reverting_order_with_unavailable_products_signals_unavailable_products()
    {
        $order = Order::factory()->state([
            'status' => OrderStatus::AwaitingPayment,
            'user_id' => $this->user->id
        ])->create();
        $product = Product::factory()->state(['stock' => 0])->create(); // No stock available
        $order->products()->attach($product->id, ['amount' => 2]);
        $res = $this->signIn($this->user)->sendRequest([
            "order_id" => $order->id
        ]);
        $this->assertDatabaseMissing('cart_product', [
            'product_id' => $product->id,
        ]);
        $res->assertOk();
        $this->assertCount(1, $res->json()['data']);
        $this->assertArrayHasKey('unavailable_products', $res->json()['data'][0]);
        $this->assertEquals($product->id, $res->json()['data'][0]['unavailable_products'][0]['product_id']);
    }

    /**
     * @test
     */
    public function reverting_order_creates_cart_if_none_exists()
    {
        $order = Order::factory()->state([
            'status' => OrderStatus::AwaitingPayment,
            'user_id' => $this->user->id
        ])->create();
        $product = Product::factory()->state(['stock' => 10])->create();
        $order->products()->attach($product->id, ['amount' => 2]);
        $res = $this->signIn($this->user)->sendRequest([
            "order_id" => $order->id
        ]);
        $cart = Cart::where('user_id', $this->user->id)->first();
        $this->assertNotNull($cart);
        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $res->assertOk();
    }

    /**
     * @test
     */
    public function reverting_order_updates_order_status_to_cancelled()
    {
        $order = Order::factory()->state([
            'status' => OrderStatus::AwaitingPayment,
            'user_id' => $this->user->id
        ])->create();
        $product = Product::factory()->state(['stock' => 10])->create();
        $order->products()->attach($product->id, ['amount' => 2]);
        $this->signIn($this->user)->sendRequest([
            "order_id" => $order->id
        ]);
        $order->refresh(); // Refresh the order to get updated data
        $this->assertEquals(OrderStatus::Cancelled, $order->status);
    }

    protected function sendRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.revert.order', $data));
    }

}
