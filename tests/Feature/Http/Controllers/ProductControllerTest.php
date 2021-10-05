<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\CreatesApplication;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProductController
 */
class ProductControllerTest extends TestCase
{
    use AdditionalAssertions, WithFaker, CreatesApplication;

    /**
     * @test
     */
    public function index_responds_with()
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.products', ['page' => 1, 'paginate' => 20, 'filters' => []]));
        $response->assertJsonFragment([
                [
                    'id' => $product->id,
                    'name' => $product->getTranslations('name'),
                    'price' => $product->price,
                    'final_price' => $product->final_price
                ]]
        );
    }

    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            ProductController::class,
            'update',
            ProductUpdateRequest::class
        );
    }

    //    /**
//     * @test
//     */
//    public function update_behaves_as_expected()
//    {
//        $product = Product::factory()->create();
//        $name = $this->faker->name;
//        $price = $this->faker->randomFloat(/** decimal_attributes **/);
//
//        $response = $this->put(route('product.update', $product), [
//            'name' => $name,
//            'price' => $price,
//        ]);
//    }
//
//
    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            ProductController::class,
            'create',
            ProductStoreRequest::class
        );
    }
//
//    /**
//     * @test
//     */
//    public function store_behaves_as_expected()
//    {
//        $name = $this->faker->name;
//        $price = $this->faker->randomFloat(/** decimal_attributes **/);
//
//        $response = $this->post(route('product.store'), [
//            'name' => $name,
//            'price' => $price,
//        ]);
//    }
//
//
    /**
     * @test
     */
    public function destroy_deletes()
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product', $product));

        $this->assertDeleted($product);
    }
}
