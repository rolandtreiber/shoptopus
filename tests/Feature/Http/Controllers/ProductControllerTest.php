<?php

namespace Tests\Feature\Http\Controllers;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProductController
 */
class ProductControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('product.index'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductController::class,
            'update',
            \App\Http\Requests\ProductUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $product = Product::factory()->create();
        $name = $this->faker->name;
        $price = $this->faker->randomFloat(/** decimal_attributes **/);

        $response = $this->put(route('product.update', $product), [
            'name' => $name,
            'price' => $price,
        ]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductController::class,
            'store',
            \App\Http\Requests\ProductStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_behaves_as_expected()
    {
        $name = $this->faker->name;
        $price = $this->faker->randomFloat(/** decimal_attributes **/);

        $response = $this->post(route('product.store'), [
            'name' => $name,
            'price' => $price,
        ]);
    }


    /**
     * @test
     */
    public function destroy_deletes()
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('product.destroy', $product));

        $this->assertDeleted($product);
    }
}
