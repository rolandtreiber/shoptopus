<?php

namespace Tests\PublicApi\Product;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\DiscountRule;
use App\Models\ProductVariant;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\Product\ProductRepository;

class GetProductTest extends TestCase
{
    use RefreshDatabase;

    protected $product;

    public function setUp() : void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_can_return_a_product_by_its_id()
    {
        $this->sendRequest()
            ->assertOk()
            ->assertSee($this->product->slug);
    }

    /**
     * @test
     * @group apiGet
     * @group apiGetBySlug
     */
    public function it_can_return_a_product_by_its_slug()
    {
        $this->getJson(route('api.products.getBySlug', ['slug' => $this->product->slug]))
            ->assertOk()
            ->assertSee($this->product->slug);
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
        $this->product->discount_rules()->attach($dr->id);

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
    public function it_returns_its_product_categories()
    {
        $pc = ProductCategory::factory()->create();
        $this->product->product_categories()->attach($pc->id);

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
     * @group apiGet
     */
    public function it_returns_its_product_tags()
    {
        $pt = ProductTag::factory()->create();
        $this->product->product_tags()->attach($pt->id);

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
     * @group apiGet
     */
    public function it_returns_its_product_variants()
    {
        $pv = ProductVariant::factory()->create(['product_id' => $this->product->id]);

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
     * @group apiGet
     */
    public function it_returns_the_associated_product_attributes_with_their_options()
    {
        $pa = ProductAttribute::factory()->create();
        ProductAttributeOption::factory()->count(3)->create(['product_attribute_id' => $pa->id]);
        $pa->products()->attach($this->product->id);

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
                                    'option_value',
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
     * @group apiGet
     */
    public function the_product_attribute_options_must_be_enabled_and_not_soft_deleted()
    {
        $pa = ProductAttribute::factory()->create();
        $pao_enabled = ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id]);
        $pao_disabled = ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id, 'enabled' => false]);
        $pao_deleted = ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id, 'deleted_at' => now()]);
        $pa->products()->attach($this->product->id);

        $res = $this->sendRequest()->json('data.0.product_attributes.0.options');

        $this->assertCount(1, $res);

        $this->assertEquals($pao_enabled->id, $res[0]['id']);
    }

    protected function getModelRepo() : ProductRepository
    {
        return new ProductRepository(new ErrorService, new Product);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.products.get', ['id' => $this->product->id]));
    }
}
