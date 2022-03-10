<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\CreatesApplication;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group products
 * @see \App\Http\Controllers\Admin\ProductController
 */
class ProductControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;

    use AdditionalAssertions, WithFaker, CreatesApplication;

    /**
     * @test
     */
    public function test_can_list_products()
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.products', ['page' => 1, 'paginate' => 20, 'filters' => []]));
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.0.id', $product->id)
                ->where('data.0.name', $product->getTranslations('name'))
                ->where('data.0.price', $product->price)
                ->where('data.0.final_price', $product->final_price)
                ->has('data', 1)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_products_can_be_filtered_by_tags()
    {
        $products = Product::factory()->count(2)->create();
        $tag = ProductTag::factory()->create();
        $products[0]->tags()->attach($tag);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.products', [
            'tags' => [$tag->id],
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.0.id', $products[0]->id)
                ->has('data', 1)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_can_create_product()
    {
        Storage::fake('uploads');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product'), [
            'name' => json_encode([
                'en' => 'Test Product',
                'de' => 'Test Produkt'
            ]),
            'price' => 12.50,
            'short_description' => json_encode([
                'en' => 'Short Description',
                'de' => 'Kurz Bezeichnung'
            ]),
            'description' => json_encode([
                'en' => 'Longer Description',
                'de' => 'Langer Bezeichnung'
            ]),
            'attachments' => [
                UploadedFile::fake()->image('product_image1.jpg'),
                UploadedFile::fake()->image('product_image2.jpg')
            ]
        ]);
        $productId = $response->json()['data']['id'];
        $product = Product::find($productId);
        Storage::disk('uploads')->assertExists($product->fileContents[0]->file_name);
        Storage::disk('uploads')->assertExists($product->fileContents[1]->file_name);
        $response->assertCreated();
        $jsonResponse = $response->json();
        $productId = $jsonResponse['data']['id'];
        $product = Product::find($productId);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->setLocale('en')->name);
        $this->assertEquals('Test Produkt', $product->setLocale('de')->name);
        $this->assertEquals('Short Description', $product->setLocale('en')->short_description);
        $this->assertEquals('Kurz Bezeichnung', $product->setLocale('de')->short_description);
        $this->assertEquals('Longer Description', $product->setLocale('en')->description);
        $this->assertEquals('Langer Bezeichnung', $product->setLocale('de')->description);
        $this->assertEquals(12.50, $product->price);
    }

    /**
     * @test
     */
    public function test_update_product_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            ProductController::class,
            'update',
            ProductUpdateRequest::class
        );
    }

    public function test_can_update_product()
    {
        Storage::fake('uploads');

        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product', [
            'product' => $product->id
        ]), [
            'name' => json_encode([
                'en' => 'Updated Test Product',
                'de' => 'Aktualisiert Test Produkt'
            ]),
            'price' => 12.33,
            'short_description' => json_encode([
                'en' => 'Updated Short Description',
                'de' => 'Aktualisiert Kurz Bezeichnung'
            ]),
            'description' => json_encode([
                'en' => 'Updated Longer Description',
                'de' => 'Aktualisiert Langer Bezeichnung'
            ]),
            'attachments' => [
                UploadedFile::fake()->image('product_image1.jpg'),
                UploadedFile::fake()->image('product_image2.jpg')
            ]
        ]);
        $response->assertCreated();
        $jsonResponse = $response->json();
        $productId = $jsonResponse['data']['id'];
        $product = Product::find($productId);
        Storage::disk('uploads')->assertExists($product->fileContents[0]->file_name);
        Storage::disk('uploads')->assertExists($product->fileContents[1]->file_name);
        $this->assertEquals('Updated Test Product', $product->setLocale('en')->name);
        $this->assertEquals('Aktualisiert Test Produkt', $product->setLocale('de')->name);
        $this->assertEquals('Updated Short Description', $product->setLocale('en')->short_description);
        $this->assertEquals('Aktualisiert Kurz Bezeichnung', $product->setLocale('de')->short_description);
        $this->assertEquals('Updated Longer Description', $product->setLocale('en')->description);
        $this->assertEquals('Aktualisiert Langer Bezeichnung', $product->setLocale('de')->description);
        $this->assertEquals(12.33, $product->price);

    }

    /**
     * @test
     */
    public function test_store_product_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            ProductController::class,
            'create',
            ProductStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function test_destroy_product_deletes()
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product', $product));

        $this->assertSoftDeleted($product);
    }
}
