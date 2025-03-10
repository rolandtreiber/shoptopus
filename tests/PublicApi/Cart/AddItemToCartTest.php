<?php

namespace Tests\PublicApi\Cart;

use App\Events\UserInteraction;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group add-item-to-cart
 */
class AddItemToCartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_has_all_required_fields(): void
    {
        $data = [
            'product_id' => null,
            'quantity' => null,
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['product_id', 'quantity']);

        $this->assertDatabaseMissing('cart_product', $data);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function the_product_must_exists(): void
    {
        $data = [
            'product_id' => '101',
            'quantity' => 2,
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['product_id', 'quantity']);
        $this->assertEquals('The selected product id is invalid.', $res->json('errors.product_id.0'));
        $this->assertEquals('Product is unavailable.', $res->json('errors.quantity.0'));
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function the_quantity_must_be_at_least_one(): void
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 0,
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('The quantity field must be at least 1.', $res->json('errors.quantity.0'));
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_returns_the_correct_error_message_when_the_product_is_out_of_stock(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $data = [
            'product_id' => $product->id,
            'quantity' => 1,
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Out of stock.', $res->json('errors.quantity.0'));
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_returns_the_correct_error_message_when_the_requested_quantity_is_unavailable(): void
    {
        $product = Product::factory()->create(['stock' => 1]);

        $data = [
            'product_id' => $product->id,
            'quantity' => 2,
        ];

        $res = $this->sendRequest($data);

        $res->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Only 1 left.', $res->json('errors.quantity.0'));

        $product2 = Product::factory()->create(['stock' => 3]);

        $data = [
            'product_id' => $product2->id,
            'quantity' => 5,
        ];

        $res2 = $this->sendRequest($data);

        $res2->assertJsonValidationErrors(['quantity']);
        $this->assertEquals('Only 3 left.', $res2->json('errors.quantity.0'));
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function if_the_cart_id_is_present_it_must_exists(): void
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 1,
            'cart_id' => 'random-cart-id',
        ];

        $this->sendRequest($data)->assertJsonValidationErrors(['cart_id']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_creates_a_new_cart_and_adds_a_new_entry_to_the_cart_product_table_for_unauthenticated_users(): void
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ];

        $this->assertDatabaseMissing('carts', [
            'user_id' => null,
        ]);

        $this->assertDatabaseMissing('cart_product', [
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);

        $res = $this->sendRequest($data)->json('data.0');

        $this->assertDatabaseHas('carts', [
            'id' => $res['id'],
            'user_id' => $res['user_id'],
        ]);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $res['id'],
            'product_id' => $res['products'][0]['id'],
            'quantity' => $res['products'][0]['quantity'],
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function existing_records_get_correctly_updated(): void
    {
        $product = Product::factory()->create();

        $cartData = $this->sendRequest([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ])->json('data.0');

        $data = [
            'cart_id' => $cartData['id'],
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ];

        $this->sendRequest($data)->json('data.0');

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cartData['id'],
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_returns_all_required_data(): void
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
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
                            "id",
                            "product_id",
                            "product_variant_id",
                            "name",
                            "item_original_price",
                            "item_final_price",
                            "subtotal_original_price",
                            "subtotal_final_price",
                            "quantity",
                            "remaining_stock",
                            "in_other_carts"
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_updates_last_seen(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $this->signIn($user);
        $cartData = $this->sendRequest([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ])->json('data.0');

        $data = [
            'cart_id' => $cartData['id'],
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ];
        $user->last_seen = null;
        $user->save();
        $this->sendRequest($data)->json('data.0');
        $user->refresh();
        $this->assertTrue($user->last_seen->timestamp <= Carbon::now()->timestamp);
    }

    /**
     * @test
     */
    public function it_triggers_user_interaction_event(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Event::fake();
        $this->signIn($user);
        $cartData = $this->sendRequest([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ])->json('data.0');

        $data = [
            'cart_id' => $cartData['id'],
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ];
        $this->sendRequest($data)->json('data.0');
        Event::assertDispatched(UserInteraction::class);
    }

    /**
     * @test
     */
    public function it_adds_different_product_variations_correctly()
    {
        $product = Product::factory()->create();
        $productVariants = ProductVariant::factory()
            ->state([
                'product_id' => $product->id,
                'stock' => 25
            ])->count(2)
            ->create();

        $data = [
            'product_id' => $product->id,
            'product_variant_id' => $productVariants[0]->id,
            'quantity' => 1,
        ];

        $cartId = $this->sendRequest($data)->json('data.0.id');

        $data = [
            'product_id' => $product->id,
            'product_variant_id' => $productVariants[1]->id,
            'quantity' => 2,
            'cart_id' => $cartId
        ];

        $response = $this->sendRequest($data);

        $response->assertJson(fn(AssertableJson $json) => $json->where('data.0.id', $cartId)
            ->where('data.0.products.0.quantity', 1)
            ->where('data.0.products.0.product_id', $product->id)
            ->where('data.0.products.0.product_variant_id', $productVariants[0]->id)
            ->where('data.0.products.1.quantity', 2)
            ->where('data.0.products.1.product_id', $product->id)
            ->where('data.0.products.1.product_variant_id', $productVariants[1]->id)
            ->etc());
    }

    /**
     * @test
     * @group apiPost
     */
    public function appropriate_variant_get_correctly_updated(): void
    {
        $product = Product::factory()->create();
        $productVariants = ProductVariant::factory()
            ->state([
                'product_id' => $product->id,
                'stock' => 25
            ])->count(2)
            ->create();

        $cartId = $this->sendRequest([
            'product_id' => $product->id,
            'product_variant_id' => $productVariants[0]->id,
            'quantity' => 1,
        ])->json('data.0.id');

        $data = [
            'cart_id' => $cartId,
            'product_id' => $product->id,
            'product_variant_id' => $productVariants[1]->id,
            'quantity' => 1,
        ];

        $this->sendRequest($data);

        $data = [
            'cart_id' => $cartId,
            'product_id' => $product->id,
            'product_variant_id' => $productVariants[0]->id,
            'quantity' => 2,
        ];

        $this->sendRequest($data);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cartId,
            'product_id' => $product->id,
            'product_variant_id' => $productVariants[0]->id,
            'quantity' => 3,
        ]);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.cart.addItem'), $data);
    }
}
