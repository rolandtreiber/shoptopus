<?php

namespace Tests\PublicApi\Product;

use App\Events\UserInteraction;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\User;
use App\Repositories\Local\Product\ProductRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class GetAllProductsTest extends TestCase
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
        Product::factory()->count(2)->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                app()->make(ProductRepository::class)->getSelectableColumns(false),
            ],
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_final_price_field(): void
    {
        $product = Product::factory()->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure(['data' => [['final_price']]]);

        $this->assertEquals($product->price, $res->json('data.0.final_price'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function soft_deleted_products_are_not_returned(): void
    {
        Product::factory()->count(2)->create(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_count(): void
    {
        Product::factory()->count(2)->create();

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_attributes_with_their_options(): void
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
     * @group apiGetAll
     */
    public function the_product_attribute_options_must_be_enabled_and_not_soft_deleted(): void
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
     *
     * @group apiGetAll
     */
    public function it_returns_the_associated_enabled_discount_rules(): void
    {
        $p = Product::factory()->create();
        $dr = DiscountRule::factory()->create([
            'valid_from' => now()->subDay()->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);
        $p->discount_rules()->attach($dr->id);

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
        $p = Product::factory()->create();

        $dr1 = DiscountRule::factory()->create([
            'valid_from' => now()->addDays(5)->toDateTimeString(),
            'valid_until' => now()->addDays(10)->toDateTimeString(),
        ]);

        $dr2 = DiscountRule::factory()->create([
            'valid_from' => now()->subDays(5)->toDateTimeString(),
            'valid_until' => now()->subDay()->toDateTimeString(),
        ]);

        $p->discount_rules()->attach([$dr1->id, $dr2->id]);

        $this->assertEmpty($this->sendRequest()->json('data.0.discount_rules'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_categories_with_their_discount_rules(): void
    {
        $p = Product::factory()->create();
        $pc = ProductCategory::factory()->create();
        $product_category_discount_rule = DiscountRule::factory()->create([
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);
        $pc->discount_rules()->attach($product_category_discount_rule->id);
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
     * @group apiGetAll
     */
    public function it_returns_its_product_tags(): void
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
     * @group apiGetAll
     */
    public function it_does_not_contain_the_product_variants(): void
    {
        $p = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $p->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_variants',
                ],
            ],
        ]);

        $this->assertEmpty($this->sendRequest()->json('data.0.product_variants'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_categories(): void
    {
        $products = Product::factory()->count(5)->create();
        $categories = ProductCategory::factory()->count(2)->create();

        $products[0]->product_categories()->attach($categories[0]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_categories' => implode(',', [$categories[0]->id, $categories[1]->id]),
        ]));

        $this->assertCount(1, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));

        $products[1]->product_categories()->attach($categories[1]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_categories' => implode(',', [$categories[0]->id, $categories[1]->id]),
        ]));

        $this->assertCount(2, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
        $this->assertEquals($products[1]->id, $res->json('data.1.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_tags(): void
    {
        $products = Product::factory()->count(5)->create();
        $tags = ProductTag::factory()->count(2)->create();

        $products[0]->product_tags()->attach($tags[0]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_tags' => implode(',', [$tags[0]->id, $tags[1]->id]),
        ]));

        $this->assertCount(1, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));

        $products[1]->product_tags()->attach($tags[1]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_tags' => implode(',', [$tags[0]->id, $tags[1]->id]),
        ]));

        $this->assertCount(2, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
        $this->assertEquals($products[1]->id, $res->json('data.1.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_multiple_relationships(): void
    {
        $products = Product::factory()->count(5)->create();
        $categories = ProductCategory::factory()->count(2)->create();
        $tags = ProductTag::factory()->count(2)->create();

        $products[0]->product_categories()->attach($categories[0]->id);
        $products[0]->product_tags()->attach($tags[0]->id);

        $res = $this->getJson(route('api.products.getAll', [
            'product_categories' => implode(',', [$categories[0]->id]),
            'product_tags' => implode(',', [$tags[0]->id]),
        ]));

        $this->assertCount(1, $res->json('data'));

        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_attribute_options(): void
    {
        $products = Product::factory()->count(5)->create();

        $attribute = ProductAttribute::factory()->create();
        $options = ProductAttributeOption::factory()->count(2)->create(['product_attribute_id' => $attribute->id]);
        $attribute->products()->attach($products[0]->id, ['product_attribute_option_id' => $options[0]->id]);
        $attribute->products()->attach($products[0]->id, ['product_attribute_option_id' => $options[1]->id]);

        $res = $this->getJson(route('api.products.getAll', [
            'options' => implode(',', [$options[0]->id, $options[1]->id]),
        ]));

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function products_can_be_filtered_by_id(): void
    {
        Product::factory()->count(3)->create();
        $product = Product::factory()->create();

        $res = $this->sendRequest(['filter[id]' => $product->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($product->id, $res->json('data.0.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters(): void
    {
        $products = Product::factory()->count(3)->create();

        $res = $this->sendRequest(['filter[id]' => implode(',', [$products[0]->id, $products[1]->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($products[0]->id, $res->json('data.0.id'));
        $this->assertEquals($products[1]->id, $res->json('data.1.id'));
    }

    /**
     * @test
     */
    public function it_updates_last_seen(): void
    {
        $user = User::factory()->create();
        $this->signIn($user);
        $user->last_seen = null;
        $user->save();
        $products = Product::factory()->count(3)->create();
        $this->sendRequest(['filter[id]' => implode(',', [$products[0]->id, $products[1]->id])]);
        $user->refresh();
        $this->assertTrue($user->last_seen->timestamp <= Carbon::now()->timestamp);
    }

    /**
     * @test
     */
    public function it_triggers_user_interaction_event(): void
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();
        Event::fake();
        $this->signIn($user);
        $user->last_seen = null;
        $user->save();
        $this->sendRequest(['filter[id]' => implode(',', [$products[0]->id, $products[1]->id])]);
        $user->refresh();
        Event::assertDispatched(UserInteraction::class);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.products.getAll', $data));
    }
}
