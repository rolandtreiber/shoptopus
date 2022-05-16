<?php

namespace Tests\PublicApi\ProductCategory;

use Tests\TestCase;
use App\Models\Product;
use App\Models\DiscountRule;
use App\Models\ProductCategory;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\ProductCategory\ProductCategoryRepository;

class GetAllProductCategoriesTest extends TestCase
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
        ProductCategory::factory()->count(2)->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                (new ProductCategoryRepository(new ErrorService, new ProductCategory))->getSelectableColumns(false)
            ]
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function soft_deleted_and_disabled_product_categories_are_not_returned()
    {
        ProductCategory::factory()->count(2)->create(['deleted_at' => now()]);
        ProductCategory::factory()->create(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_count()
    {
        ProductCategory::factory()->count(2)->create();

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_discount_rules()
    {
        $pc = ProductCategory::factory()->create();
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
    public function it_returns_its_subcategories()
    {
        $pc = ProductCategory::factory()->create();
        $subcategories = ProductCategory::factory()->count(3)->create(['parent_id' => $pc->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'subcategories' => [
                        (new ProductCategoryRepository(new ErrorService, new ProductCategory))->getSelectableColumns(false)
                    ]
                ]
            ]
        ]);

        $this->assertCount(3, $res->json('data.0.subcategories'));

        $subcategories[0]->update(['enabled' => false]);
        $subcategories[1]->update(['deleted_at' => now()]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.subcategories'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_ids()
    {
        $pc = ProductCategory::factory()->create();
        $p = Product::factory()->create();
        $p2 = Product::factory()->create();
        $pc->products()->attach([$p->id, $p2->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_ids'
                ]
            ]
        ]);

        $this->assertCount(2, $res->json('data.0.product_ids'));

        $p2->update(['deleted_at' => now()]);

        $this->assertCount(1,$this->sendRequest()->json('data.0.product_ids'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function product_categories_can_be_filtered_by_id()
    {
        ProductCategory::factory()->count(3)->create();
        $product_category = ProductCategory::factory()->create();

        $res = $this->sendRequest(['filter[id]' => $product_category->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($product_category->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters()
    {
        ProductCategory::factory()->count(3)->create();
        $product_category1 = ProductCategory::factory()->create();
        $product_category2 = ProductCategory::factory()->create();

        $res = $this->sendRequest(['filter[id]' => implode(',', [$product_category1->id, $product_category2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($product_category1->id, $res->json('data.0.id'));
        $this->assertEquals($product_category2->id, $res->json('data.1.id'));
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_categories.getAll', $data));
    }
}
