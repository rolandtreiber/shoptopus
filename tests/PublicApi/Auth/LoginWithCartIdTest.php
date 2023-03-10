<?php

namespace Tests\PublicApi\Auth;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginWithCartIdTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');

        $this->user = User::factory()->create();
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_existing_cart_of_the_user_is_correctly_merged()
    {
        $cart_front_end = Cart::factory()->create(['user_id' => null]);
        $product_front_end = Product::factory()->create();
        $cart_front_end->products()->attach($product_front_end->id, ['quantity' => 1]);

        $product_back_end = Product::factory()->create();
        $this->user->cart->products()->attach($product_back_end->id, ['quantity' => 1]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart_front_end->id,
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $this->user->cart->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart_front_end->id,
            'product_id' => $product_front_end->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $this->user->cart->id,
            'product_id' => $product_back_end->id,
            'quantity' => 1,
        ]);

        $data = [
            'email' => $this->user->email,
            'password' => 'password',
            'cart_id' => $cart_front_end->id,
        ];

        $cart = $this->signIn($this->user)
            ->sendRequest($data)
            ->json('data.auth.user.cart');

        $this->assertCount(2, $cart['products']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function when_carts_are_merged_the_quantity_for_the_same_product_is_correctly_updated()
    {
        $product = Product::factory()->create();

        $cart_front_end = Cart::factory()->create(['user_id' => null]);
        $cart_front_end->products()->attach($product->id, ['quantity' => 1]);

        $this->user->cart->products()->attach($product->id, ['quantity' => 1]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart_front_end->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $this->user->cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $data = [
            'email' => $this->user->email,
            'password' => 'password',
            'cart_id' => $cart_front_end->id,
        ];

        $cart = $this->signIn($this->user)
            ->sendRequest($data)
            ->json('data.auth.user.cart');

        $this->assertCount(1, $cart['products']);

        $this->assertEquals('2', $cart['products'][0]['quantity']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_old_cart_gets_deleted()
    {
        $cart_front_end = Cart::factory()->create(['user_id' => null]);
        $product_front_end = Product::factory()->create();
        $cart_front_end->products()->attach($product_front_end->id, ['quantity' => 1]);

        $product_back_end = Product::factory()->create();
        $this->user->cart->products()->attach($product_back_end->id, ['quantity' => 1]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart_front_end->id,
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $this->user->cart->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart_front_end->id,
            'product_id' => $product_front_end->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $this->user->cart->id,
            'product_id' => $product_back_end->id,
            'quantity' => 1,
        ]);

        $data = [
            'email' => $this->user->email,
            'password' => 'password',
            'cart_id' => $cart_front_end->id,
        ];

        $this->signIn($this->user)
            ->sendRequest($data)
            ->json('data.auth.user.cart');

        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart_front_end->id,
            'product_id' => $product_front_end->id,
        ]);

        $this->assertDatabaseMissing('carts', [
            'id' => $cart_front_end->id,
        ]);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.auth.login'), $data);
    }
}
