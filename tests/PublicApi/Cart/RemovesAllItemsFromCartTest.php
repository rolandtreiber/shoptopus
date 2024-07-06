<?php

namespace Tests\PublicApi\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group removes-all-products-from-cart
 */
class RemovesAllItemsFromCartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_empties_cart_by_cart_id(): void
    {
        $product = Product::factory()->create();

        $cart = Cart::factory()->create();
        $cart->products()->attach($product->id);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $data = [
            'cart_id' => $cart->id,
        ];

        $this->sendRequest($data)->json();

        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $this->assertEmpty($cart->products);

    }

    /**
     * @test
     */
    public function it_empties_cart_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $cart = Cart::factory()->state(['user_id' => $user->id])->create();
        $cart->products()->attach($product->id);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $data = [
            'cart_id' => $user->cart->id,
        ];

        $this->signIn($user)->sendRequest($data)->json();

        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $this->assertEmpty($cart->products);

    }

    /**
     * @test
     */
    public function it_fails_if_cart_id_doesn_match_authenticated_users_cart(): void
    {
        $user = User::factory()->create();
        Cart::factory()->state(['user_id' => $user->id])->create();
        $cartNoAuth = Cart::factory()->create();

        $data = [
            'cart_id' => $cartNoAuth->id,
        ];

        $res = $this->signIn($user)->sendRequest($data);

        $res->assertJsonFragment([
            'developer_message' => 'This action is unauthorized.'
        ]);
    }

    /**
     * @test
     */
    public function it_fails_if_cart_id_is_invalid(): void
    {
        $data = [
            'cart_id' => "INVALID",
        ];

        $error = $this->sendRequest($data)->json('errors.cart_id.0');

        $this->assertStringContainsString("cart id is invalid", $error);
    }

    /**
     * @test
     */
    public function it_fails_if_cart_id_is_missing(): void
    {
        $error = $this->sendRequest()->json('errors.cart_id.0');

        $this->assertStringContainsString("required", $error);
    }

    protected function sendRequest($data = []): TestResponse
    {
        return $this->deleteJson(route('api.cart.removeAll'), $data);
    }

}
