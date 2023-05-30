<?php

namespace Tests\Unit\Repositories;

use App\Enums\DiscountType;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Repositories\Local\Product\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repo;

    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = app()->make(ProductRepository::class);

        $this->product = Product::factory()->create(['price' => 10.00]);
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_a_discount_rule_is_applied(): void
    {
        $discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 5.55,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach($discount_rule->id);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(4.45, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_allowed(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', true);

        $discount_rule1 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 3,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $discount_rule2 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 2,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach([$discount_rule1->id, $discount_rule2->id]);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(5, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_disallowed(): void
    {
        $discount_rule1 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 5,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $discount_rule2 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 6,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach([$discount_rule1->id, $discount_rule2->id]);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(4, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_disallowed_and_the_highest_rule_is_set_to_be_applied(): void
    {
        $discount_rule1 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 4,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $discount_rule2 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 6,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach([$discount_rule1->id, $discount_rule2->id]);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(4, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_disallowed_and_the_lowest_rule_is_set_to_be_applied(): void
    {
        Config::set('shoptopus.discount_rules.applied_discount', 'lowest');

        $discount_rule1 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 4,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $discount_rule2 = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 6,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach([$discount_rule1->id, $discount_rule2->id]);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(6, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_it_has_a_discount_rule_and_a_category_discount_rule(): void
    {
        $product_category = ProductCategory::factory()->create();
        $product_category_discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 6,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);
        $product_category->discount_rules()->attach($product_category_discount_rule->id);
        $this->product->product_categories()->attach($product_category->id);

        $discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 5,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);
        $this->product->discount_rules()->attach($discount_rule->id);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(4, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_it_has_a_discount_rule_and_also_a_category_with_its_own_discount_rule_and_stacking_is_allowed(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', true);

        $product_category = ProductCategory::factory()->create();
        $product_category_discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 3,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);
        $product_category->discount_rules()->attach($product_category_discount_rule->id);
        $this->product->product_categories()->attach($product_category->id);

        $discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 3,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach($discount_rule->id);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(4, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_it_has_a_percentage_based_discount_rule_and_also_a_category_with_its_own_discount_rule_and_stacking_is_disallowed(): void
    {
        $product_category = ProductCategory::factory()->create();
        $product_category_discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Percentage,
            'amount' => 50, // 50%
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);
        $product_category->discount_rules()->attach($product_category_discount_rule->id);
        $this->product->product_categories()->attach($product_category->id);

        $discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 5.5,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach($discount_rule->id);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(4.5, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group repo
     */
    public function it_calculates_the_final_price_correctly_when_it_has_a_percentage_based_discount_rule_and_also_a_category_with_its_own_discount_rule_and_stacking_is_allowed(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', true);

        $product_category = ProductCategory::factory()->create();
        $product_category_discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Percentage,
            'amount' => 10, // 10%
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);
        $product_category->discount_rules()->attach($product_category_discount_rule->id);
        $this->product->product_categories()->attach($product_category->id);

        $discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 3,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach($discount_rule->id);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals(6, $this->getFinalPrice($product));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_calculates_the_final_price_correctly_for_the_product_variants(): void
    {
        $pv = ProductVariant::factory()->create(['product_id' => $this->product->id]);

        $product = $this->repo->getTheResultWithRelationships([$this->product->toArray()])[0];

        $this->assertEquals($pv->price, $this->getFinalPrice($product, $pv->price));
    }

    protected function getFinalPrice($product, $product_variant_price = false): string
    {
        return $this->repo->calculateFinalPrice($product, $product_variant_price);
    }
}
