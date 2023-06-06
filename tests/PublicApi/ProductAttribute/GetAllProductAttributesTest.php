<?php

namespace Tests\PublicApi\ProductAttribute;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Repositories\Local\ProductAttribute\ProductAttributeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllProductAttributesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_correct_format(): void
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'message',
                'data',
                'next',
                'records',
                'total_records',
            ]);
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_all_required_fields(): void
    {
        $product = Product::factory()->create();
        $attributes = ProductAttribute::factory()->count(2)->create();
        $option1 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attributes[0]->id]);
        $option2 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attributes[1]->id]);
        $attributes[0]->products()->attach($product->id, ['product_attribute_option_id' => $option1->id]);
        $attributes[1]->products()->attach($product->id, ['product_attribute_option_id' => $option2->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                app()->make(ProductAttributeRepository::class)->getSelectableColumns(false),
            ],
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function soft_deleted_and_disabled_product_attributes_are_not_returned(): void
    {
        $product = Product::factory()->create();
        $attribute1 = ProductAttribute::factory()->create(['deleted_at' => now()]);
        $attribute2 = ProductAttribute::factory()->create(['enabled' => false]);
        $option1 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attribute1->id]);
        $option2 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attribute2->id]);
        $attribute1->products()->attach($product->id, ['product_attribute_option_id' => $option1->id]);
        $attribute2->products()->attach($product->id, ['product_attribute_option_id' => $option2->id]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_count(): void
    {
        $product = Product::factory()->create();
        $attributes = ProductAttribute::factory()->count(2)->create();
        $option1 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attributes[0]->id]);
        $option2 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attributes[1]->id]);
        $attributes[0]->products()->attach($product->id, ['product_attribute_option_id' => $option1->id]);
        $attributes[1]->products()->attach($product->id, ['product_attribute_option_id' => $option2->id]);

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_associated_options(): void
    {
        $product = Product::factory()->create();
        $pa = ProductAttribute::factory()->create();
        $pao = ProductAttributeOption::factory()->create();
        $pao2 = ProductAttributeOption::factory()->create();
        $pa->options()->saveMany([$pao, $pao2]);
        $pa->products()->attach($product->id, ['product_attribute_option_id' => $pao->id]);
        $pa->products()->attach($product->id, ['product_attribute_option_id' => $pao2->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'options' => [
                        [
                            'id',
                            'name',
                            'slug',
                            'value',
                            'image',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(2, $res->json('data.0.options'));

        $pao->update(['enabled' => false]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.options'));

        $pao->update(['enabled' => true, 'deleted_at' => now()]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.options'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function attributes_without_options_are_excluded(): void
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $attribute->products()->attach($product->id);

        $res = $this->sendRequest();

        $this->assertEmpty($res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function attributes_without_products_are_excluded(): void
    {
        ProductAttribute::factory()->count(3)->create();

        $res = $this->sendRequest();

        $this->assertEmpty($res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_ids(): void
    {
        $pa = ProductAttribute::factory()->create();
        $option = ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id]);
        $p = Product::factory()->create();
        $p2 = Product::factory()->create();
        $pa->products()->attach([$p->id, $p2->id], ['product_attribute_option_id' => $option->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_ids',
                ],
            ],
        ]);

        $this->assertCount(2, $res->json('data.0.product_ids'));

        $p2->update(['deleted_at' => now()]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.product_ids'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_can_filter_attributes_within_a_product_category(): void
    {
        $products_without_category = Product::factory()->count(2)->create();
        $products_in_category = Product::factory()->count(2)->create();
        $product_category = ProductCategory::factory()->create();
        $product_category->products()->saveMany([$products_in_category[0], $products_in_category[1]]);

        $attribute_for_products_without_category = ProductAttribute::factory()->create();
        $attribute_for_products_in_category = ProductAttribute::factory()->create();

        $option1 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attribute_for_products_without_category->id]);
        $option2 = ProductAttributeOption::factory()->create(['product_attribute_id' => $attribute_for_products_in_category->id]);

        $attribute_for_products_without_category->products()->attach(
            $products_without_category->pluck('id')->toArray(), ['product_attribute_option_id' => $option1->id]
        );
        $attribute_for_products_in_category->products()->attach(
            $products_in_category->pluck('id')->toArray(), ['product_attribute_option_id' => $option2->id]
        );

        $this->assertEquals(2, ProductAttribute::count());

        $res = $this->getJson(route('api.product_attributes.getAllForProductCategory',
            ['product_category_id' => $product_category->id])
        );

        $this->assertCount(1, $res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function product_attributes_can_be_filtered_by_id(): void
    {
        $product = Product::factory()->create();
        $pas = ProductAttribute::factory()->count(3)->create();
        $option1 = ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[0]->id]);
        $option2 = ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[1]->id]);
        $option3 = ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[2]->id]);

        $pas[0]->products()->attach($product->id, ['product_attribute_option_id' => $option1->id]);
        $pas[1]->products()->attach($product->id, ['product_attribute_option_id' => $option2->id]);
        $pas[2]->products()->attach($product->id, ['product_attribute_option_id' => $option3->id]);

        $res = $this->sendRequest(['filter[id]' => $pas[0]->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($pas[0]->id, $res->json('data.0.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters(): void
    {
        $product = Product::factory()->create();
        $pas = ProductAttribute::factory()->count(3)->create();
        $option1 = ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[0]->id]);
        $option2 = ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[1]->id]);
        $option3 = ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[2]->id]);

        $pas[0]->products()->attach($product->id, ['product_attribute_option_id' => $option1->id]);
        $pas[1]->products()->attach($product->id, ['product_attribute_option_id' => $option2->id]);
        $pas[2]->products()->attach($product->id, ['product_attribute_option_id' => $option3->id]);

        $res = $this->sendRequest(['filter[id]' => implode(',', [$pas[0]->id, $pas[1]->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($pas[0]->id, $res->json('data.0.id'));
        $this->assertEquals($pas[1]->id, $res->json('data.1.id'));
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_attributes.getAll', $data));
    }
}
