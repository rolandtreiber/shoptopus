<?php

namespace Tests\PublicApi\Product;

use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use Tests\TestCase;
use App\Models\Product;
use App\Models\DiscountRule;
use App\Services\Local\Error\ErrorService;
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
                $this->getModelRepo()->getSelectableColumns(false)
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

        $this->assertEquals(2, $this->signIn()->sendRequest()->json('total_records'));
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
        $pc = ProductCategory::factory()->create();
        $p = Product::factory()->create();
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
        $pt = ProductTag::factory()->create();
        $p = Product::factory()->create();
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
    public function it_returns_its_product_variants()
    {
        $p = Product::factory()->create();
        $pv = ProductVariant::factory()->create(['product_id' => $p->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_variants' => [
                        [
                            'id',
                            'slug',
                            'price',
                            'data',
                            'stock',
                            'sku',
                            'description'
                        ]
                    ]
                ]
            ]
        ]);

        $pv->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_variants'));

        $pv->update(['enabled' => false, 'deleted_at' => null]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_variants'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_id()
    {
        Product::factory()->count(3)->create();
        $product = Product::factory()->create();

        $res = $this->signIn()->sendRequest(['filter[id]' => $product->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($product->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters()
    {
        Product::factory()->count(3)->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $res = $this->signIn()->sendRequest(['filter[id]' => implode(',', [$product1->id, $product2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($product1->id, $res->json('data.0.id'));
        $this->assertEquals($product2->id, $res->json('data.1.id'));
    }

    protected function getModelRepo() : ProductRepository
    {
        return new ProductRepository(new ErrorService, new Product);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.products.getAll', $data));
    }
}
