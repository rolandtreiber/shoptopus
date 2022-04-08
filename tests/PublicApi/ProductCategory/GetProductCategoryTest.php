<?php

namespace Tests\PublicApi\ProductCategory;

use Tests\TestCase;
use App\Models\Product;
use App\Models\DiscountRule;
use App\Models\ProductCategory;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\ProductCategory\ProductCategoryRepository;

class GetProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $product_category;

    public function setUp() : void
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
     */
    public function it_returns_all_required_fields()
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    $this->getModelRepo()->getSelectableColumns(false)
                ]
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_enabled_discount_rules()
    {
        $dr = DiscountRule::factory()->create();
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
     * @group apiGet
     */
    public function it_returns_the_associated_products()
    {
        $p = Product::factory()->create();
        $this->product_category->products()->attach($p->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'products' => [
                        [
                            'id',
                            'slug',
                            'name',
                            'short_description',
                            'description',
                            'price',
                            'status',
                            'purchase_count',
                            'stock',
                            'backup_stock',
                            'sku',
                            'cover_photo'
                        ]
                    ]
                ]
            ]
        ]);

        $p->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.products'));
    }

    protected function getModelRepo() : ProductCategoryRepository
    {
        return new ProductCategoryRepository(new ErrorService, new ProductCategory);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_categories.get', ['id' => $this->product_category->id]));
    }
}
