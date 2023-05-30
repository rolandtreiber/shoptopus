<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Enums\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AdminControllerTestCase;
use Throwable;

/**
 * @group admin-base-crud
 * @group product_attributes
 *
 * @see \App\Http\Controllers\Admin\ProductAttributeController
 */
class ProductAttributeControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;
    // Happy

    /**
     * @test
     */
    public function test_product_attributes_can_be_listed(): void
    {
        $attributes = ProductAttribute::factory()->count(2)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.product-attributes', [
            'page' => 1,
            'paginate' => 20,
            'filters' => [],
        ]));
        $response->assertJsonFragment([
            'id' => $attributes[0]->id,
        ]);
        $response->assertJsonFragment([
            'id' => $attributes[1]->id,
        ]);
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function test_product_attribute_can_be_shown(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $options = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id,
        ])->count(2)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.product-attribute', [
            'attribute' => $attribute->id,
        ]));
        $response->assertJsonFragment([
            'id' => $attribute->id,
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.id', $attribute->id)
                ->has('data.options', 2)
                ->where('data.options.0.id', $options[0]->id)
                ->where('data.options.1.id', $options[1]->id)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_product_attribute_can_be_created(): void
    {
        Storage::fake('uploads');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-attribute'), [
            'name' => json_encode([
                'en' => 'Color',
                'de' => 'Farbe',
            ]),
            'image' => UploadedFile::fake()->image('product_attribute.jpg'),
            'type' => ProductAttributeType::Text,
        ]);
        $response->assertCreated();
        $attributeId = $response->json()['data']['id'];
        $attribute = ProductAttribute::find($attributeId);
        Storage::disk('uploads')->assertExists($attribute->image->file_name);
        $this->assertEquals('Color', $attribute->setLocale('en')->name);
        $this->assertEquals('Farbe', $attribute->setLocale('de')->name);
    }

    /**
     * @test
     */
    public function test_product_attributes_can_be_updated(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-attribute', [
            'attribute' => $attribute,
        ]), [
            'name' => json_encode([
                'en' => 'Color',
                'de' => 'Farbe',
            ]),
            'image' => UploadedFile::fake()->image('product_attribute.jpg'),
            'type' => ProductAttributeType::Text,
        ]);
        $attributeId = $response->json()['data']['id'];
        $this->assertEquals($attributeId, $attribute->id);
        $attribute = ProductAttribute::find($attributeId);
        Storage::disk('uploads')->assertExists($attribute->image->file_name);
        $this->assertEquals('Color', $attribute->setLocale('en')->name);
        $this->assertEquals('Farbe', $attribute->setLocale('de')->name);
    }

    /**
     * @test
     */
    public function test_product_attributes_can_be_deleted(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $options = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id,
        ])->count(2)->create();

        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product-attribute', $attribute));

        $this->assertSoftDeleted($attribute);
        $this->assertSoftDeleted($options[0]);
        $this->assertSoftDeleted($options[1]);
    }

    /**
     * @test
     */
    public function test_product_attribute_option_can_be_created(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-attribute-option', [
            'attribute' => $attribute,
        ]), [
            'name' => json_encode([
                'en' => 'Red',
                'de' => 'Rot',
            ]),
            'image' => UploadedFile::fake()->image('red.jpg'),
            'value' => 1,
        ]);
        $attributeOptionId = $response->json()['data']['id'];
        $option = ProductAttributeOption::find($attributeOptionId);
        Storage::disk('uploads')->assertExists($option->image->file_name);
        $this->assertEquals('Red', $option->setLocale('en')->name);
        $this->assertEquals('Rot', $option->setLocale('de')->name);
        $this->assertEquals($attribute->id, $option->product_attribute_id);
    }

    /**
     * @test
     */
    public function test_product_attribute_option_can_be_updated(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $option = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-attribute-option', [
            'attribute' => $attribute,
            'option' => $option,
        ]), [
            'name' => json_encode([
                'en' => 'Yellow',
                'de' => 'Gelb',
            ]),
            'image' => UploadedFile::fake()->image('red.jpg'),
            'value' => 1,
        ]);
        $attributeOptionId = $response->json()['data']['id'];
        $this->assertEquals($option->id, $attributeOptionId);
        $option = ProductAttributeOption::find($attributeOptionId);
        Storage::disk('uploads')->assertExists($option->image->file_name);
        $this->assertEquals('Yellow', $option->setLocale('en')->name);
        $this->assertEquals('Gelb', $option->setLocale('de')->name);
        $this->assertEquals($attribute->id, $option->product_attribute_id);
    }

    /**
     * @test
     */
    public function test_product_attribute_option_can_be_deleted(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $option = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product-attribute-option', [
            'attribute' => $attribute,
            'option' => $option,
        ]));

        $this->assertSoftDeleted($option);
    }

    // Unhappy

    /**
     * @test
     */
    public function test_product_attribute_creation_validation(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-attribute'), [
            'name' => json_encode([
                'en' => 'Color',
                'de' => 'Farbe',
            ]),
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_product_attribute_option_creation_validation(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-attribute', [
            'attribute' => $attribute,
        ]), [
            'name' => json_encode([
                'en' => 'Red',
                'de' => 'Rot',
            ]),
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_product_attribute_deletion_requires_the_right_Permission(): void
    {
        $attribute = ProductAttribute::factory()->create();

        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->delete(route('admin.api.delete.product-attribute', $attribute));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_product_attribute_option_deletion_requires_the_right_Permission(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $option = ProductAttributeOption::factory()->state([
            'product_attribute_id' => $attribute->id,
        ])->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->delete(route('admin.api.delete.product-attribute-option', [
            'attribute' => $attribute,
            'option' => $option,
        ]));
        $response->assertForbidden();
    }
}
