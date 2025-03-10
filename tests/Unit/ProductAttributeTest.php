<?php

namespace Tests\Unit;

use App\Enums\ProductAttributeType;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductAttributeTest extends TestCase
{
    use RefreshDatabase;

    protected $product_attribute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product_attribute = ProductAttribute::factory()->create();
    }

    /** @test */
    public function it_has_a_name_field(): void
    {
        $this->assertNotNull($this->product_attribute->name);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_name_field(): void
    {
        $this->assertEquals(Str::slug($this->product_attribute->name), $this->product_attribute->slug);
    }

    /** @test */
    public function it_returns_a_translated_name(): void
    {
        $this->product_attribute
            ->setTranslation('name', 'en', 'english translation')
            ->setTranslation('name', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product_attribute->name);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product_attribute->name);
    }

    /** @test */
    public function it_has_a_type_field(): void
    {
        $this->assertEquals(ProductAttributeType::Text, $this->product_attribute->type);
    }

    /** @test */
    public function it_has_image_field(): void
    {
        $this->assertNull($this->product_attribute->image);
    }

    /** @test */
    public function it_has_an_enabled_field(): void
    {
        $this->assertTrue($this->product_attribute->enabled);
    }

    /** @test */
    public function it_may_have_many_products(): void
    {
        $this->assertCount(0, $this->product_attribute->products);

        $product = Product::factory()->create();

        $this->product_attribute->products()->attach($product->id);

        $this->assertCount(1, $this->product_attribute->fresh()->products);

        $this->assertInstanceOf(Product::class, $this->product_attribute->products()->first());
    }

    /** @test */
    public function it_may_have_many_options(): void
    {
        $this->assertCount(0, $this->product_attribute->options);

        ProductAttributeOption::factory()->create([
            'product_attribute_id' => $this->product_attribute->id,
        ]);

        $this->assertCount(1, $this->product_attribute->fresh()->options);

        $this->assertInstanceOf(ProductAttributeOption::class, $this->product_attribute->options()->first());
    }
}
