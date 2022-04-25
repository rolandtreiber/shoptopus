<?php

namespace Tests\Unit;

use App\Models\DiscountRule;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\FileContent;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $product;

    public function setUp() : void
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
    function it_returns_a_translated_name()
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
    function it_returns_a_translated_short_description()
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
    function it_returns_a_translated_description()
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
    public function it_may_have_many_images()
    {
        $this->assertCount(0, $this->product->images());

        FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id
        ]);

        $this->assertInstanceOf(FileContent::class, $this->product->images()->first());
    }

    /** @test */
    public function it_creates_a_cover_photo_from_its_existing_images()
    {
        $this->assertNull($this->product->cover_photo);

        $first_image = FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id
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
            'fileable_id' => $this->product->id
        ]);

        $this->product->save();

        $this->assertTrue($this->product->cover_photo->url === $first_image->url);

        $second_image = FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id
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

        $category = ProductAttribute::factory()->create();

        $this->product->product_attributes()->attach($category->id);

        $this->assertCount(1, $this->product->fresh()->product_attributes);

        $this->assertInstanceOf(ProductAttribute::class, $this->product->product_attributes()->first());
    }

    /** @test */
    public function it_may_have_many_valid_discount_rules()
    {
        $this->assertCount(0, $this->product->discount_rules);

        $discount_rule = DiscountRule::factory()->create([
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString()
        ]);

        $this->product->discount_rules()->attach($discount_rule->id);

        $this->assertCount(1, $this->product->fresh()->discount_rules);

        $this->assertInstanceOf(DiscountRule::class, $this->product->discount_rules()->first());

        $discount_rule->update([
            'valid_until' => now()->subDays(5)->toDateTimeString()
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
