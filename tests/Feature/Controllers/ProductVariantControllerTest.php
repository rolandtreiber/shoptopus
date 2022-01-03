<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @group product_variants
 * @see \App\Http\Controllers\Admin\ProductVariantController
 */
class ProductVariantControllerTest extends TestCase
{
    // Happy

    /**
     * @test
     */
    public function test_variant_can_be_listed()
    {
        $product = Product::factory()->create();
        $variants = ProductVariant::factory()->state([
            'product_id' => $product->id
        ])->count(3)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.product-variants', [
            'product' => $product->id,
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $variants[0]->id
        ]);
        $response->assertJsonFragment([
            'id' => $variants[1]->id
        ]);
        $response->assertJsonFragment([
            'id' => $variants[2]->id
        ]);
    }

    /**
     * @test
     */
    public function test_variant_can_be_created()
    {
        Storage::fake('uploads');
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $attributeOption = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-variant', [
            'product' => $product->id
        ]), [
            'price' => 12.9,
            'description' => json_encode([
                'en' => 'Test Variant',
                'de' => 'Test Variante'
            ]),
            'product_attributes' => [
                $attribute->id => $attributeOption->id
            ],
            'attachments' => [
                UploadedFile::fake()->image('product_variant_image1.jpg'),
                UploadedFile::fake()->image('product_variant_image2.jpg')
            ]
        ]);
        $productVariantId = $response->json()['data']['id'];
        $productVariant = ProductVariant::find($productVariantId);
        Storage::disk('uploads')->assertExists($productVariant->fileContents[0]->file_name);
        Storage::disk('uploads')->assertExists($productVariant->fileContents[1]->file_name);
        $this->assertEquals(12.9, $productVariant->price);
        $this->assertEquals('Test Variant', $productVariant->setLocale('en')->description);
        $this->assertEquals('Test Variante', $productVariant->setLocale('de')->description);
        $this->assertTrue($productVariant->productVariantAttributes->contains($attribute));
    }

    /**
     * @test
     */
    public function test_variant_can_be_updated()
    {
        Storage::fake('uploads');
        $product = Product::factory()->create();
        $attribute1 = ProductAttribute::factory()->create();
        $attribute2 = ProductAttribute::factory()->create();
        $attributeOption1 = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute1->id
        ])->create();
        $attributeOption2 = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute1->id
        ])->create();
        $productVariant = ProductVariant::factory()->state([
            'product_id' => $product->id
        ])->create();
        $productVariant->productVariantAttributes()->attach($attribute1, ['product_attribute_option_id' => $attributeOption1->id]);

        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-variant', [
            'product' => $product->id,
            'variant' => $productVariant->id
        ]), [
            'price' => 11.12,
            'description' => json_encode([
                'en' => 'Updated Test Variant',
                'de' => 'Aktualisiert Test Variante'
            ]),
            'product_attributes' => [
                $attribute2->id => $attributeOption2->id
            ],
            'attachments' => [
                UploadedFile::fake()->image('product_variant_image1.jpg'),
                UploadedFile::fake()->image('product_variant_image2.jpg')
            ]
        ]);
        $productVariantId = $response->json()['data']['id'];
        $productVariant = ProductVariant::find($productVariantId);
        Storage::disk('uploads')->assertExists($productVariant->fileContents[0]->file_name);
        Storage::disk('uploads')->assertExists($productVariant->fileContents[1]->file_name);
        $this->assertEquals(11.12, $productVariant->price);
        $this->assertEquals('Updated Test Variant', $productVariant->setLocale('en')->description);
        $this->assertEquals('Aktualisiert Test Variante', $productVariant->setLocale('de')->description);
        $this->assertTrue($productVariant->productVariantAttributes->contains($attribute2));
        $this->assertFalse($productVariant->productVariantAttributes->contains($attribute1));
    }

    /**
     * @test
     */
    public function test_variant_can_be_shown()
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $attributeOption = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id
        ])->create();
        $productVariant = ProductVariant::factory()->state([
            'product_id' => $product->id
        ])->create();
        $productVariant->productVariantAttributes()->attach($attribute, ['product_attribute_option_id' => $attributeOption->id]);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.product-variant', [
            'product' => $product->id,
            'variant' => $productVariant->id
        ]));
        $variantId = $response->json()['data']['id'];
        $attributeId = $response->json()['data']['attributes'][0]['id'];
        $optionId = $response->json()['data']['attributes'][0]['option']['id'];
        $this->assertEquals($variantId, $productVariant->id);
        $this->assertEquals($attributeId, $attribute->id);
        $this->assertEquals($optionId, $attributeOption->id);
    }

    /**
     * @test
     */
    public function test_variant_can_be_deleted()
    {
        $product = Product::factory()->create();
        $productVariant = ProductVariant::factory()->state([
            'product_id' => $product->id
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product-variant', [
            'product' => $product->id,
            'variant' => $productVariant->id
        ]));

        $this->assertSoftDeleted($productVariant);
    }

    // Unhappy
    /**
     * @test
     */
    public function test_variant_creation_validation()
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-variant', [
            'product' => $product->id
        ]));
        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function test_variant_update_validation()
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $attributeOption = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id
        ])->create();
        $productVariant = ProductVariant::factory()->state([
            'product_id' => $product->id
        ])->create();
        $productVariant->productVariantAttributes()->attach($attribute, ['product_attribute_option_id' => $attributeOption->id]);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-variant', [
            'product' => $product->id,
            'variant' => $productVariant->id
        ]), [
            'price' => 'twelve'
        ]);
        $response->assertStatus(302);
    }

}
