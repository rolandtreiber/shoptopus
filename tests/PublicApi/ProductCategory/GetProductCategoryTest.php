<?php

namespace Tests\PublicApi\ProductCategory;

use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\Local\ProductCategory\ProductCategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $product_category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product_category = ProductCategory::factory()->create();
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_can_return_a_product_category_by_its_id()
    {
        $this->sendRequest()
            ->assertOk()
            ->assertSee($this->product_category->description);
    }

    /**
     * @test
     * @group apiGet
     * @group apiGetBySlug
     */
    public function it_can_return_a_product_category_by_its_slug()
    {
        $this->getJson(route('api.product_category.getBySlug', ['slug' => $this->product_category->slug]))
            ->assertOk()
            ->assertSee($this->product_category->description);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_all_required_fields()
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    app()->make(ProductCategoryRepository::class)->getSelectableColumns(false),
                ],
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_discount_rules()
    {
        $dr = DiscountRule::factory()->create([
            'valid_from' => now()->subDay()->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);
        $this->product_category->discount_rules()->attach($dr->id);

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
     * @group apiGet
     */
    public function the_discount_rules_must_be_valid()
    {
        $dr1 = DiscountRule::factory()->create([
            'valid_from' => now()->addDays(5)->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);

        $dr2 = DiscountRule::factory()->create([
            'valid_from' => now()->subDays(5)->toDateTimeString(),
            'valid_until' => now()->subDay()->toDateTimeString(),
        ]);

        $this->product_category->discount_rules()->attach([$dr1->id, $dr2->id]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_its_subcategories()
    {
        $subcategories = ProductCategory::factory()->count(3)->create(['parent_id' => $this->product_category->id]);

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
     * @group apiGet
     */
    public function it_returns_the_associated_product_ids()
    {
        $p = Product::factory()->create();
        $this->product_category->products()->attach($p->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_ids',
                ],
            ],
        ]);

        $this->assertEquals($p->id, $res->json('data.0.product_ids.0'));

        $p->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_ids'));
    }

    protected function sendRequest(): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_category.get', ['id' => $this->product_category->id]));
    }
}
