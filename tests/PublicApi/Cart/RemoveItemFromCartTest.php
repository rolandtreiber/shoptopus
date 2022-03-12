<?php

namespace Tests\PublicApi\Cart;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RemoveItemFromCartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group apiDelete
     */
    public function it_has_all_required_fields()
    {
        $data = [
            'product_id' => null,
            'cart_id' => null
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['product_id', 'cart_id']);

        $this->assertDatabaseMissing('cart_product', $data);
    }

    /**
     * @test
     * @group apiDelete
     */
    public function the_product_must_exists()
    {
        $cart = Cart::factory()->create();

        $data = [
            'product_id' => '101',
            'cart_id' => $cart->id
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['product_id']);
        $this->assertEquals('The selected product id is invalid.', $res->json('errors.product_id.0'));
    }

    /**
     * @test
     * @group apiDelete
     */
    public function if_authenticated_the_cart_must_belong_to_the_user()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->products()->attach($product->id);

        $data = [
            'product_id' => $product->id,
            'cart_id' => $cart->id
        ];

        $res = $this->signIn()->sendRequest($data)->json();

        $this->assertEquals('This action is unauthorized.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     * @group apiDelete
     */
    public function it_removes_the_product_from_the_cart()
    {
        $product = Product::factory()->create();

        $cart = Cart::factory()->create();
        $cart->products()->attach($product->id);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $data = [
            'product_id' => $product->id,
            'cart_id' => $cart->id
        ];

        $this->sendRequest($data)->json();

        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $this->assertEmpty($cart->products);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('api.cart.removeItem'), $data);
    }
}
