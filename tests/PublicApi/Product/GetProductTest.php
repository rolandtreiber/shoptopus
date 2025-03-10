<?php

namespace Tests\PublicApi\Product;

use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Repositories\Local\Product\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetProductTest extends TestCase
{
    use RefreshDatabase;

    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_can_return_a_product_by_its_id(): void
    {
        $this->sendRequest()
            ->assertOk()
            ->assertSee($this->product->slug);
    }

    /**
     * @test
     *
     * @group apiGet
     * @group apiGetBySlug
     */
    public function it_can_return_a_product_by_its_slug(): void
    {
        $this->getJson(route('api.product.getBySlug', ['slug' => $this->product->slug]))
            ->assertOk()
            ->assertSee($this->product->slug);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_all_required_fields(): void
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    app()->make(ProductRepository::class)->getSelectableColumns(false),
                ],
            ]);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_the_final_price_field(): void
    {
        $res = $this->sendRequest();

        $res->assertJsonStructure(['data' => [['final_price']]]);

        $this->assertEquals($this->product->price, $res->json('data.0.final_price'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_the_associated_enabled_discount_rules(): void
    {
        $dr = DiscountRule::factory()->create([
            'valid_from' => now()->subDay()->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);
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
     * @group apiGet
     */
    public function the_discount_rules_must_be_valid(): void
    {
        $dr1 = DiscountRule::factory()->create([
            'valid_from' => now()->addDays(5)->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);

        $dr2 = DiscountRule::factory()->create([
            'valid_from' => now()->subDays(5)->toDateTimeString(),
            'valid_until' => now()->subDay()->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach([$dr1->id, $dr2->id]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_its_product_categories_with_their_discount_rules(): void
    {
        $pc = ProductCategory::factory()->create();
        $product_category_discount_rule = DiscountRule::factory()->create([
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);
        $pc->discount_rules()->attach($product_category_discount_rule->id);

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
                            'header_image',
                            'discount_rules' => [
                                [
                                    'id',
                                    'type',
                                    'amount',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $pc->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_categories'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_its_product_tags(): void
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
                            'display_badge',
                        ],
                    ],
                ],
            ],
        ]);

        $pt->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_tags'));

        $pt->update(['enabled' => false, 'deleted_at' => null]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_tags'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_its_product_variants(): void
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
                            'description',
                        ],
                    ],
                ],
            ],
        ]);

        $pv->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_variants'));

        $pv->update(['enabled' => false, 'deleted_at' => null]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_variants'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_a_final_price_attribute_for_the_product_variants(): void
    {
        $pv = ProductVariant::factory()->create(['product_id' => $this->product->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure(['data' => [['product_variants' => [['final_price']]]]]);

        $this->assertEquals($pv->price, $this->sendRequest()->json('data.0.product_variants.0.final_price'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_all_the_attributes_with_their_corresponding_options_for_the_product_variants(): void
    {
        $pv = ProductVariant::factory()->create(['product_id' => $this->product->id]);
        $pa_valid = ProductAttribute::factory()->create();
        $pa_disabled = ProductAttribute::factory()->create(['enabled' => false]);
        $pa_deleted = ProductAttribute::factory()->create(['deleted_at' => now()]);
        $attribute_options = ProductAttributeOption::factory()->count(3)->create(['product_attribute_id' => $pa_valid->id]);

        $pv->product_variant_attributes()->attach($pa_valid->id, ['product_attribute_option_id' => $attribute_options[0]->id]);
        $pv->product_variant_attributes()->attach($pa_disabled->id, ['product_attribute_option_id' => $attribute_options[1]->id]);
        $pv->product_variant_attributes()->attach($pa_deleted->id, ['product_attribute_option_id' => $attribute_options[2]->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_variants' => [
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
                                            'image',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $res->json('data.0.product_variants.0.product_attributes'));
        $this->assertEquals($pa_valid->id, $res->json('data.0.product_variants.0.product_attributes.0.id'));

        $this->assertCount(1, $res->json('data.0.product_variants.0.product_attributes.0.options'));

        $attribute_options->first()->update(['enabled' => false]);

        $this->assertCount(0, $this->sendRequest()->json('data.0.product_variants.0.product_attributes.0.options'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_the_associated_product_attributes_with_their_options(): void
    {
        $pa = ProductAttribute::factory()->create();
        $paos = ProductAttributeOption::factory()->count(3)->create(['product_attribute_id' => $pa->id]);
        $pa->products()->attach($this->product->id);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));

        DB::table('product_product_attribute')
            ->where('product_attribute_id', $pa->id)
            ->where('product_id', $this->product->id)
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
                                    'image',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $pa->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));

        $pa->update(['deleted_at' => null, 'enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function the_product_attribute_options_must_be_enabled_and_not_soft_deleted(): void
    {
        $pa = ProductAttribute::factory()->create();
        $pao = ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id]);
        $pa->products()->attach($this->product->id, ['product_attribute_option_id' => $pao->id]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.product_attributes.0.options'));

        $pao->update(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));

        $pao->update(['enabled' => true, 'deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_attributes'));
    }

    protected function sendRequest(): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product.get', ['id' => $this->product->id]));
    }
}
