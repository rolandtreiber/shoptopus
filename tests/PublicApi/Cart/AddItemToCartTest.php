<?php

namespace Tests\PublicApi\Cart;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddItemToCartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        $data = [
            'product_id' => null,
            'quantity' => null
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['product_id', 'quantity']);

        $this->assertDatabaseMissing('cart_product', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_product_must_exists()
    {
        $data = [
            'product_id' => '101',
            'quantity' => 2
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['product_id', 'quantity']);
        $this->assertEquals('The selected product id is invalid.', $res->json('errors.product_id.0'));
        $this->assertEquals('Product is unavailable.', $res->json('errors.quantity.0'));
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_quantity_must_be_at_least_one()
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 0
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
            'product_id' => $product->id,
            'quantity' => 1
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
            'product_id' => $product->id,
            'quantity' => 2
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Only 1 left.', $res->json('errors.quantity.0'));

        $product2 = Product::factory()->create(['stock' => 3]);

        $data = [
            'product_id' => $product2->id,
            'quantity' => 5
        ];

        $res2 = $this->sendRequest($data);

        $res2->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Only 3 left.', $res2->json('errors.quantity.0'));
    }

    /**
     * @test
     * @group apiPost
     */
    public function if_the_cart_id_is_present_it_must_exists()
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 1,
            'cart_id' => 'random-cart-id'
        ];

        $this->sendRequest($data)->assertJsonValidationErrors(['cart_id']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_creates_a_new_cart_and_adds_a_new_entry_to_the_cart_product_table_for_unauthenticated_users()
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 1
        ];

        $this->assertDatabaseMissing('carts', [
            'user_id' => null
        ]);

        $this->assertDatabaseMissing('cart_product', [
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity']
        ]);

        $res = $this->sendRequest($data)->json('data.0');

        $this->assertDatabaseHas('carts', [
            'id' => $res['id'],
            'user_id' => $res['user_id']
        ]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $res['id'],
            'product_id' => $res['products'][0]['id'],
            'quantity' => $res['products'][0]['quantity']
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function existing_records_get_correctly_updated()
    {
        $product = Product::factory()->create();

        $cartData = $this->sendRequest([
            'product_id' => $product->id,
            'quantity' => 1
        ])->json('data.0');

        $data = [
            'cart_id' => $cartData['id'],
            'product_id' => $product->id,
            'quantity' => 1
        ];

        $this->sendRequest($data)->json('data.0');

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cartData['id'],
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_returns_all_required_data()
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 1
        ];

        $this->sendRequest($data)->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'user_id',
                    'ip_address',
                    'user',
                    'products' => [
                        [
                            'product_variant_id',
                            'quantity',
                            'id',
                            'name',
                            'short_description',
                            'description',
                            'price',
                            'status',
                            'purchase_count',
                            'stock',
                            'backup_stock',
                            'sku',
                            'cover_photo',
                            'rating'
                        ]
                    ]
                ]
            ]
        ]);
    }


    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.cart.addItem'), $data);
    }
}
