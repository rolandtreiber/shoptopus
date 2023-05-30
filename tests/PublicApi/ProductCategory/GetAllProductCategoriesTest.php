<?php

namespace Tests\PublicApi\ProductCategory;

use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\Local\ProductCategory\ProductCategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllProductCategoriesTest extends TestCase
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
        ProductCategory::factory()->count(2)->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                app()->make(ProductCategoryRepository::class)->getSelectableColumns(false),
            ],
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function soft_deleted_and_disabled_product_categories_are_not_returned(): void
    {
        ProductCategory::factory()->count(2)->create(['deleted_at' => now()]);
        ProductCategory::factory()->create(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_count(): void
    {
        ProductCategory::factory()->count(2)->create();

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_associated_discount_rulessss(): void
    {
        $pc = ProductCategory::factory()->create();
        $dr = DiscountRule::factory()->create([
            'valid_from' => now()->subDay()->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);
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
                            'slug',
                        ],
                    ],
                ],
            ],
        ]);

        $dr->update(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));

        $dr->update(['enabled' => true, 'deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function the_discount_rules_must_be_valid(): void
    {
        $pc = ProductCategory::factory()->create();

        $dr1 = DiscountRule::factory()->create([
            'valid_from' => now()->addDays(5)->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);

        $dr2 = DiscountRule::factory()->create([
            'valid_from' => now()->subDays(5)->toDateTimeString(),
            'valid_until' => now()->subDay()->toDateTimeString(),
        ]);

        $pc->discount_rules()->attach([$dr1->id, $dr2->id]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_its_subcategories(): void
    {
        $pc = ProductCategory::factory()->create();
        $subcategories = ProductCategory::factory()->count(3)->create(['parent_id' => $pc->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'subcategories' => [
                        app()->make(ProductCategoryRepository::class)->getSelectableColumns(false),
                    ],
                ],
            ],
        ]);

        $this->assertCount(3, $res->json('data.0.subcategories'));

        $subcategories[0]->update(['enabled' => false]);
        $subcategories[1]->update(['deleted_at' => now()]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.subcategories'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_ids(): void
    {
        $pc = ProductCategory::factory()->create();
        $p = Product::factory()->create();
        $p2 = Product::factory()->create();
        $pc->products()->attach([$p->id, $p2->id]);

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
    public function product_categories_can_be_filtered_by_id(): void
    {
        ProductCategory::factory()->count(3)->create();
        $product_category = ProductCategory::factory()->create();

        $res = $this->sendRequest(['filter[id]' => $product_category->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($product_category->id, $res->json('data.0.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters(): void
    {
        ProductCategory::factory()->count(3)->create();
        $product_category1 = ProductCategory::factory()->create();
        $product_category2 = ProductCategory::factory()->create();

        $res = $this->sendRequest(['filter[id]' => implode(',', [$product_category1->id, $product_category2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($product_category1->id, $res->json('data.0.id'));
        $this->assertEquals($product_category2->id, $res->json('data.1.id'));
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_categories.getAll', $data));
    }
}
