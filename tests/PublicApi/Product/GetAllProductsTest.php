<?php

namespace Tests\PublicApi\Product;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\DiscountRule;
use App\Models\ProductVariant;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Models\ProductAttributeOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\Product\ProductRepository;

class GetAllProductsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_correct_format()
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'message',
                'data',
                'next',
                'records',
                'total_records'
            ]);
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_all_required_fields()
    {
        Product::factory()->count(2)->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                app()->make(ProductRepository::class)->getSelectableColumns(false)
            ]
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function soft_deleted_products_are_not_returned()
    {
        Product::factory()->count(2)->create(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_count()
    {
        Product::factory()->count(2)->create();

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_attributes_with_their_options()
    {
        $p = Product::factory()->create();
        $pa = ProductAttribute::factory()->create();
        $paos = ProductAttributeOption::factory()->count(3)->create(['product_attribute_id' => $pa->id]);
        $pa->products()->attach($p->id);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));

        DB::table('product_product_attribute')
            ->where('product_attribute_id', $pa->id)
            ->where('product_id', $p->id)
            ->update(['product_attribute_option_id' => $paos[0]->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_attributes' => [
                        [
                            'id',
                            'name',
                            'slug',
                            'type',
                            'image',
                            'options' => [
                                [
                                    'id',
                                    'name',
                                    'slug',
                                    'value',
                                    'image'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $pa->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));

        $pa->update(['deleted_at' => null, 'enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function the_product_attribute_options_must_be_enabled_and_not_soft_deleted()
    {
        $p = Product::factory()->create();
        $pa = ProductAttribute::factory()->create();
        $pao = ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id]);
        $pa->products()->attach($p->id, ['product_attribute_option_id' => $pao->id]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.product_attributes.0.options'));

        $pao->update(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));

        $pao->update(['enabled' => true, 'deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_enabled_discount_rules()
    {
        $pc = Product::factory()->create();
        $dr = DiscountRule::factory()->create();
        $pc->discount_rules()->attach($dr->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'discount_rules' => [
                        [
                            'id',
                            'type',
                            'name',
                            'amount',
                            'valid_from',
                            'valid_until',
                            'slug'
                        ]
                    ]
                ]
            ]
        ]);

        $dr->update(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));

        $dr->update(['enabled' => true, 'deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_categories()
    {
        $p = Product::factory()->create();
        $pc = ProductCategory::factory()->create();
        $pc->products()->attach($p->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_categories' => [
                        [
                            'id',
                            'slug',
                            'name',
                            'parent_id',
                            'description',
                            'menu_image',
                            'header_image'
                        ]
                    ]
                ]
            ]
        ]);

        $pc->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_categories'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_its_product_tags()
    {
        $p = Product::factory()->create();
        $pt = ProductTag::factory()->create();
        $p->product_tags()->attach($pt->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_tags' => [
                        [
                            'id',
                            'slug',
                            'name',
                            'description',
                            'badge',
                            'display_badge'
                        ]
                    ]
                ]
            ]
        ]);

        $pt->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_tags'));

        $pt->update(['enabled' => false, 'deleted_at' => null]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_tags'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_does_not_contain_the_product_variants()
    {
        $p = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $p->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_variants'
                ]
            ]
        ]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_variants'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_categories()
    {
        $products = Product::factory()->count(5)->create();
        $categories = ProductCategory::factory()->count(2)->create();

        $products[0]->product_categories()->attach($categories[0]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_categories' => implode(',', [$categories[0]->id, $categories[1]->id])
        ]));

        $this->assertCount(1, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));

        $products[1]->product_categories()->attach($categories[1]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_categories' => implode(',', [$categories[0]->id, $categories[1]->id])
        ]));

        $this->assertCount(2, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
        $this->assertEquals($products[1]->id, $res->json('data.1.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_tags()
    {
        $products = Product::factory()->count(5)->create();
        $tags = ProductTag::factory()->count(2)->create();

        $products[0]->product_tags()->attach($tags[0]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_tags' => implode(',', [$tags[0]->id, $tags[1]->id])
        ]));

        $this->assertCount(1, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));

        $products[1]->product_tags()->attach($tags[1]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_tags' => implode(',', [$tags[0]->id, $tags[1]->id])
        ]));

        $this->assertCount(2, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
        $this->assertEquals($products[1]->id, $res->json('data.1.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_multiple_relationships()
    {
        $products = Product::factory()->count(5)->create();
        $categories = ProductCategory::factory()->count(2)->create();
        $tags = ProductTag::factory()->count(2)->create();

        $products[0]->product_categories()->attach($categories[0]->id);
        $products[0]->product_tags()->attach($tags[0]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_categories' => implode(',', [$categories[0]->id]),
            'product_tags' => implode(',', [$tags[0]->id])
        ]));

        $this->assertCount(1, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_attribute_options()
    {
        $products = Product::factory()->count(5)->create();

        $attribute = ProductAttribute::factory()->create();
        $options = ProductAttributeOption::factory()->count(2)->create(['product_attribute_id' => $attribute->id]);
        $attribute->products()->attach($products[0]->id, ['product_attribute_option_id' => $options[0]->id]);
        $attribute->products()->attach($products[0]->id, ['product_attribute_option_id' => $options[1]->id]);

        $res = $this->getJson(route('api.products.getAll', [
            'options' => implode(',', [$options[0]->id, $options[1]->id])
        ]));

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_id()
    {
        Product::factory()->count(3)->create();
        $product = Product::factory()->create();

        $res = $this->sendRequest(['filter[id]' => $product->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($product->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters()
    {
        $products = Product::factory()->count(3)->create();

        $res = $this->sendRequest(['filter[id]' => implode(',', [$products[0]->id, $products[1]->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
        $this->assertEquals($products[1]->id, $res->json('data.1.id'));
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.products.getAll', $data));
    }
}
