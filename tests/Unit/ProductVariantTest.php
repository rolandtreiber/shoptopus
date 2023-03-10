<?php

namespace Tests\Unit;

use App\Models\FileContent;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    protected $product_variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product_variant = ProductVariant::factory()->create();
    }

    /** @test */
    public function it_has_a_price_field()
    {
        $this->assertNotNull($this->product_variant->price);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_products_name_field()
    {
        $this->assertEquals(Str::slug($this->product_variant->product->name), $this->product_variant->slug);
    }

    /** @test */
    public function it_has_a_description_field()
    {
        $this->assertNotNull($this->product_variant->description);
    }

    /** @test */
    public function it_returns_a_translated_description()
    {
        $this->product_variant
            ->setTranslation('description', 'en', 'english translation')
            ->setTranslation('description', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product_variant->description);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product_variant->description);
    }

    /** @test */
    public function it_belongs_to_product()
    {
        $this->assertInstanceOf(Product::class, $this->product_variant->product);
    }

    /** @test */
    public function it_has_an_enabled_field()
    {
        $this->assertTrue($this->product_variant->enabled);
    }

    /** @test */
    public function it_has_a_final_price_attribute()
    {
        $this->assertNotNull($this->product_variant->final_price);
    }

    /** @test */
    public function it_may_have_a_cover_image()
    {
        $this->assertNull($this->product_variant->cover_image());

        FileContent::factory()->create([
            'fileable_type' => get_class($this->product_variant),
            'fileable_id' => $this->product_variant->id,
        ]);

        $this->assertNotNull($this->product_variant->cover_image());

        $this->assertInstanceOf(FileContent::class, $this->product_variant->cover_image());
    }

    /** @test */
    public function it_may_have_many_product_variant_attributes()
    {
        $this->assertEmpty($this->product_variant->product_variant_attributes);

        $attr = ProductAttribute::factory()->create();
        $attr_option = ProductAttributeOption::factory()->create(['product_attribute_id' => $attr->id]);

        $this->product_variant->product_variant_attributes()->attach($attr->id, [
            'product_attribute_option_id' => $attr_option->id,
        ]);

        $this->assertNotNull($this->product_variant->product_variant_attributes);

        $attr_variant = $this->product_variant->product_variant_attributes()->first();

        $this->assertInstanceOf(ProductAttribute::class, $attr_variant);

        $this->assertInstanceOf(ProductAttributeOption::class, $attr_variant->pivot->option);
    }

    /** @test */
    public function it_updates_the_parent_product_stock_value_correctly_when_saved()
    {
        $this->product_variant->product->update(['stock' => 5]);

        $this->product_variant->update(['stock' => 2]);

        $this->assertEquals(2, $this->product_variant->product->stock);
    }

    /** @test */
    public function it_updates_the_parent_product_stock_value_correctly_when_deleted()
    {
        $this->product_variant->delete();

        $this->assertEquals(0, $this->product_variant->product->stock);
    }
}
