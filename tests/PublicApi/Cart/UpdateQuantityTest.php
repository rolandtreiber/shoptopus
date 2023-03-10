<?php

namespace Tests\PublicApi\Cart;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UpdateQuantityTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $cart;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->cart = $user->cart;
    }

    /**
     * @test
     * @group apiPatch
     */
    public function it_requires_a_valid_cart_id_and_a_valid_product_id()
    {
        $data = [
            'cart_id' => '1234',
            'product_id' => '1234',
            'quantity' => 1,
        ];

        $this->sendRequest($data)->assertJsonValidationErrors(['cart_id', 'product_id']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_quantity_must_be_at_least_one()
    {
        $product = Product::factory()->create();

        $data = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 0,
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('The quantity must be at least 1.', $res->json('errors.quantity.0'));
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_returns_the_correct_error_message_when_the_product_is_out_of_stock()
    {
        $product = Product::factory()->create(['stock' => 0]);

        $data = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Out of stock.', $res->json('errors.quantity.0'));
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_returns_the_correct_error_message_when_the_requested_quantity_is_unavailable()
    {
        $product = Product::factory()->create(['stock' => 1]);

        $data = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Only 1 left.', $res->json('errors.quantity.0'));

        $product2 = Product::factory()->create(['stock' => 3]);

        $data = [
            'cart_id' => $this->cart->id,
            'product_id' => $product2->id,
            'quantity' => 5,
        ];

        $res2 = $this->sendRequest($data);

        $res2->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Only 3 left.', $res2->json('errors.quantity.0'));
    }

    /**
     * @test
     * @group apiPatch
     */
    public function the_product_must_exists_in_the_cart()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $data = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ];

        $res = $this->sendRequest($data)->json();

        $this->assertEquals('Cart or product cannot be found.', $res['developer_message']);
        $this->assertEquals('Sorry there was an error updating the quantity for the given product.', $res['user_message']);
    }

    /**
     * @test
     * @group apiPatch
     */
    public function the_quantity_of_the_product_updates_correctly()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $cart_product = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ];

        DB::table('cart_product')->insert($cart_product);

        $this->assertDatabaseHas('cart_product', $cart_product);

        $data = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ];

        $this->sendRequest($data)->json();

        $this->assertDatabaseHas('cart_product', $data);
    }

    /**
     * @test
     * @group apiPatch
     */
    public function it_returns_the_full_cart()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $cart_product = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ];

        DB::table('cart_product')->insert($cart_product);

        $data = [
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ];

        $cart = $this->sendRequest($data)->json('data.0');

        $this->assertEquals($this->cart->id, $cart['id']);
        $this->assertEquals($product->id, $cart['products'][0]['id']);
        $this->assertEquals($data['quantity'], $cart['products'][0]['quantity']);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('api.cart.updateQuantity', [
            'cart_id' => $data['cart_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]));
    }
}
