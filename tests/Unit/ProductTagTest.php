<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\ProductTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @group product_tag
 */
class ProductTagTest extends TestCase
{
    use RefreshDatabase;

    protected $product_tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product_tag = ProductTag::factory()->create();
    }

    /** @test */
    public function it_has_a_name_field(): void
    {
        $this->assertNotNull($this->product_tag->name);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_name_field(): void
    {
        $this->assertEquals(Str::slug($this->product_tag->name), $this->product_tag->slug);
    }

    /** @test */
    public function it_returns_a_translated_name(): void
    {
        $this->product_tag
            ->setTranslation('name', 'en', 'english translation')
            ->setTranslation('name', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product_tag->name);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product_tag->name);
    }

    /** @test */
    public function it_has_a_description_field(): void
    {
        $this->assertEmpty($this->product_tag->description);
    }

    /** @test */
    public function it_returns_a_translated_description(): void
    {
        $this->product_tag
            ->setTranslation('description', 'en', 'english translation')
            ->setTranslation('description', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product_tag->description);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product_tag->description);
    }

    /** @test */
    public function it_has_a_badge_field(): void
    {
        $this->assertNull($this->product_tag->badge);
    }

    /** @test */
    public function it_has_a_display_badge_field(): void
    {
        $this->assertFalse($this->product_tag->display_badge);
    }

    /** @test */
    public function it_has_an_enabled_field(): void
    {
        $this->assertTrue($this->product_tag->enabled);
    }

    /** @test */
    public function it_may_have_many_products(): void
    {
        $this->assertCount(0, $this->product_tag->products);

        $product = Product::factory()->create();

        $this->product_tag->products()->attach($product->id);

        $this->assertCount(1, $this->product_tag->fresh()->products);

        $this->assertInstanceOf(Product::class, $this->product_tag->products()->first());
    }
}
