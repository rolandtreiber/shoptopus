<?php

namespace PublicApi\Checkout;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group check-product-availabilities
 */
class CheckProductAvailabilitiesTest extends TestCase
{
    use RefreshDatabase;

    private Cart $cart;
    private Collection $products;
    private Collection $productVariants;

    protected function setUp(): void
    {
        parent::setUp();

        $cart = Cart::factory()->create();
        $products = Product::factory()->state(['stock' => 3, 'virtual' => false])->count(3)->create();
        $productVariants = ProductVariant::factory()->state(['stock' => 2])->count(2)->create();
        /** @var ProductVariant $productVariant */
        foreach ($productVariants as $productVariant) {
            $productVariant->product->virtual = false;
            $productVariant->product->save();
        }
        $cart->products()->attach($products[0], ['quantity' => 3]);
        $cart->products()->attach($products[1], ['quantity' => 3]);
        $cart->products()->attach($products[2], ['quantity' => 3]);
        $cart->products()->attach($productVariants[0]->product, ['quantity' => 2, 'product_variant_id' => $productVariants[0]->id]);
        $cart->products()->attach($productVariants[1]->product, ['quantity' => 2, 'product_variant_id' => $productVariants[1]->id]);
        $this->products = $products;
        $this->productVariants = $productVariants;
        $this->cart = $cart;
    }

    /**
     * @test
     */
    public function correct_response_returned_if_all_is_available(): void
    {
        $res = $this->sendRequest($this->cart->id);
        $this->assertEquals("OK", $res->json('data.status'));
    }

    /**
     * @test
     */
    public function correct_response_returned_if_a_non_variable_product_is_not_available(): void
    {
        $this->products[0]->stock = 1;
        $this->products[0]->save();
        $this->products[0]->refresh();
        $res = $this->sendRequest($this->cart->id);
        $this->assertEquals("REVIEW", $res->json('data.status'));
        $res->assertJson(fn (AssertableJson $json) => $json
            ->where('data.products_to_review.0.product_id', $this->products[0]->id)
            ->where('data.products_to_review.0.available', 1)
            ->where('data.products_to_review.0.requested', 3)
            ->etc());
    }

    /**
     * @test
     */
    public function correct_response_returned_if_a_variable_product_is_not_available(): void
    {
        $this->productVariants[1]->stock = 1;
        $this->productVariants[1]->save();
        $this->productVariants[1]->refresh();
        $res = $this->sendRequest($this->cart->id);
        $this->assertEquals("REVIEW", $res->json('data.status'));
        $res->assertJson(fn (AssertableJson $json) => $json
            ->where('data.products_to_review.0.product_id', $this->productVariants[1]->product->id)
            ->where('data.products_to_review.0.available', 1)
            ->where('data.products_to_review.0.requested', 2)
            ->etc());
    }

    /**
     * @test
     */
    public function correct_response_returned_if_mixed_variable_and_non_variable_products_are_unavailable(): void
    {
        $this->products[0]->stock = 1;
        $this->products[0]->save();
        $this->productVariants[1]->stock = 1;
        $this->productVariants[1]->save();
        $res = $this->sendRequest($this->cart->id);
        $this->assertEquals("REVIEW", $res->json('data.status'));
        $res->assertJson(fn (AssertableJson $json) => $json
            ->where('data.products_to_review.0.product_id', $this->products[0]->id)
            ->where('data.products_to_review.0.available', 1)
            ->where('data.products_to_review.0.requested', 3)
            ->where('data.products_to_review.1.product_id', $this->productVariants[1]->product->id)
            ->where('data.products_to_review.1.available', 1)
            ->where('data.products_to_review.1.requested', 2)
            ->etc());
    }

    protected function sendRequest($cartId): TestResponse
    {
        return $this->getJson(route('api.checkout.get.check-availabilities', [
            'cart' => $cartId
        ]));
    }

}
