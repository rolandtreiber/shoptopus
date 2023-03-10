<?php

namespace Tests\Unit;

use App\Enums\DiscountType;
use App\Enums\ProductStatus;
use App\Models\DiscountRule;
use App\Models\FileContent;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
    }

    /** @test */
    public function it_has_a_name_field()
    {
        $this->assertNotNull($this->product->name);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_name_field()
    {
        $this->assertEquals(Str::slug($this->product->name), $this->product->slug);
    }

    /** @test */
    public function it_returns_a_translated_name()
    {
        $this->product
            ->setTranslation('name', 'en', 'english translation')
            ->setTranslation('name', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product->name);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product->name);
    }

    /** @test */
    public function it_has_a_short_description_field()
    {
        $this->assertNotNull($this->product->short_description);
    }

    /** @test */
    public function it_returns_a_translated_short_description()
    {
        $this->product
            ->setTranslation('short_description', 'en', 'english translation')
            ->setTranslation('short_description', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product->short_description);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product->short_description);
    }

    /** @test */
    public function it_has_a_description_field()
    {
        $this->assertNotNull($this->product->description);
    }

    /** @test */
    public function it_returns_a_translated_description()
    {
        $this->product
            ->setTranslation('description', 'en', 'english translation')
            ->setTranslation('description', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product->description);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product->description);
    }

    /** @test */
    public function it_has_a_price_field()
    {
        $this->assertNotNull($this->product->price);
    }

    /** @test */
    public function it_has_a_status_field()
    {
        $this->assertEquals(ProductStatus::Active, $this->product->status);
    }

    /** @test */
    public function it_has_a_purchase_count_field()
    {
        $this->assertNotNull($this->product->purchase_count);
    }

    /** @test */
    public function it_has_a_stock_field()
    {
        $this->assertNotNull($this->product->stock);
    }

    /** @test */
    public function it_has_a_backup_stock_field()
    {
        $this->assertNotNull($this->product->backup_stock);
    }

    /** @test */
    public function it_has_a_sku_field()
    {
        $this->assertNotNull($this->product->sku);
    }

    /** @test */
    public function it_has_a_headline_field()
    {
        $this->assertEmpty($this->product->headline);
    }

    /** @test */
    public function it_has_a_subtitle_field()
    {
        $this->assertEmpty($this->product->subtitle);
    }

    /** @test */
    public function it_has_a_deleted_at_field()
    {
        $this->assertNull($this->product->deleted_at);
    }

    /** @test */
    public function it_has_a_final_price_attribute()
    {
        $this->assertNotNull($this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_a_discount_rule_is_applied()
    {
        $this->product->update(['price' => 10.00]);

        $discount_rule = DiscountRule::factory()->create([
            'type' => DiscountType::Amount,
            'amount' => 5.55,
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach($discount_rule->id);

        $this->assertEquals(4.45, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_allowed()
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', true);

        $this->product->update(['price' => 10.00]);

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

        $this->assertEquals(5, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_disallowed()
    {
        $this->product->update(['price' => 10.00]);

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

        $this->assertEquals(4, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_disallowed_and_the_highest_rule_is_set_to_be_applied()
    {
        $this->product->update(['price' => 10.00]);

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

        $this->assertEquals(4, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_multiple_discount_rules_are_applied_and_stacking_is_disallowed_and_the_lowest_rule_is_set_to_be_applied()
    {
        Config::set('shoptopus.discount_rules.applied_discount', 'lowest');

        $this->product->update(['price' => 10.00]);

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

        $this->assertEquals(6, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_it_has_a_discount_rule_and_also_a_category_with_its_own_discount_rule()
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

        $this->product->update(['price' => 10.00]);

        $this->assertEquals(4, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_it_has_a_discount_rule_and_also_a_category_with_its_own_discount_rule_and_stacking_is_allowed()
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

        $this->product->update(['price' => 10.00]);

        $this->assertEquals(4, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_it_has_a_percentage_based_discount_rule_and_also_a_category_with_its_own_discount_rule_and_stacking_is_disallowed()
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

        $this->product->update(['price' => 10.00]);

        $this->assertEquals(4.5, $this->product->final_price);
    }

    /** @test */
    public function its_final_price_attribute_is_calculated_correctly_when_it_has_a_percentage_based_discount_rule_and_also_a_category_with_its_own_discount_rule_and_stacking_is_allowed()
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

        $this->product->update(['price' => 10.00]);

        $this->assertEquals(6, $this->product->final_price);
    }

    /** @test */
    public function it_may_have_many_images()
    {
        $this->assertCount(0, $this->product->images());

        FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id,
        ]);

        $this->assertInstanceOf(FileContent::class, $this->product->images()->first());
    }

    /** @test */
    public function it_creates_a_cover_photo_from_its_existing_images()
    {
        $this->assertNull($this->product->cover_photo);

        $first_image = FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id,
        ]);

        $this->product->save();

        $this->assertNotNull($this->product->cover_photo);

        $this->assertTrue($this->product->cover_photo->url === $first_image->url);
    }

    /** @test */
    public function the_cover_photo_will_always_be_the_first_image()
    {
        $first_image = FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id,
        ]);

        $this->product->save();

        $this->assertTrue($this->product->cover_photo->url === $first_image->url);

        $second_image = FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id,
        ]);

        $this->product->save();

        $this->assertTrue($this->product->cover_photo->url === $first_image->url);
    }

    /** @test */
    public function it_may_have_many_tags()
    {
        $this->assertCount(0, $this->product->product_tags);

        $tag = ProductTag::factory()->create();

        $this->product->product_tags()->attach($tag->id);

        $this->assertCount(1, $this->product->fresh()->product_tags);

        $this->assertInstanceOf(ProductTag::class, $this->product->product_tags()->first());
    }

    /** @test */
    public function it_may_have_many_categories()
    {
        $this->assertCount(0, $this->product->product_categories);

        $category = ProductCategory::factory()->create();

        $this->product->product_categories()->attach($category->id);

        $this->assertCount(1, $this->product->fresh()->product_categories);

        $this->assertInstanceOf(ProductCategory::class, $this->product->product_categories()->first());
    }

    /** @test */
    public function it_may_have_many_attributes()
    {
        $this->assertCount(0, $this->product->product_attributes);

        $attribute = ProductAttribute::factory()->create();

        $this->product->product_attributes()->attach($attribute->id);

        $this->assertCount(1, $this->product->fresh()->product_attributes);

        $this->assertInstanceOf(ProductAttribute::class, $this->product->product_attributes()->first());
    }

    /** @test */
    public function it_may_have_many_valid_discount_rules()
    {
        $this->assertCount(0, $this->product->discount_rules);

        $discount_rule = DiscountRule::factory()->create([
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product->discount_rules()->attach($discount_rule->id);

        $this->assertCount(1, $this->product->fresh()->discount_rules);

        $this->assertInstanceOf(DiscountRule::class, $this->product->discount_rules()->first());

        $discount_rule->update([
            'valid_until' => now()->subDays(5)->toDateTimeString(),
        ]);

        $this->assertCount(0, $this->product->fresh()->discount_rules);
    }

    /** @test */
    public function it_may_have_many_product_variants()
    {
        $this->assertCount(0, $this->product->product_variants);

        ProductVariant::factory()->create(['product_id' => $this->product->id]);

        $this->assertCount(1, $this->product->fresh()->product_variants);

        $this->assertInstanceOf(ProductVariant::class, $this->product->product_variants()->first());
    }
}
