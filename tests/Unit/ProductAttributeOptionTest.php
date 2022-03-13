<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\ProductAttribute;
use App\Enums\ProductAttributeType;
use App\Models\ProductAttributeOption;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductAttributeOptionTest extends TestCase
{
    use RefreshDatabase;

    protected $product_attribute_option;

    public function setUp() : void
    {
        parent::setUp();

        $this->product_attribute_option = ProductAttributeOption::factory()->create();
    }

    /** @test */
    public function it_has_a_name_field()
    {
        $this->assertNotNull($this->product_attribute_option->name);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_name_field()
    {
        $this->assertEquals(Str::slug($this->product_attribute_option->name), $this->product_attribute_option->slug);
    }

    /** @test */
    function it_returns_a_translated_name()
    {
        $this->product_attribute_option
            ->setTranslation('name', 'en', 'english translation')
            ->setTranslation('name', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product_attribute_option->name);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product_attribute_option->name);
    }

    /** @test */
    public function it_has_a_value_field()
    {
        $this->assertNotNull($this->product_attribute_option->value);
    }

    /** @test */
    public function it_has_image_field()
    {
        $this->assertNull($this->product_attribute_option->image);
    }

    /** @test */
    public function it_has_an_enabled_field()
    {
        $this->assertTrue($this->product_attribute_option->enabled);
    }

    /** @test */
    public function it_may_belongs_to_a_product_attribute()
    {
        $this->assertNull($this->product_attribute_option->product_attribute);

        $this->product_attribute_option->update([
            'product_attribute_id' => ProductAttribute::factory()->create()->id
        ]);

        $this->assertInstanceOf(ProductAttribute::class, $this->product_attribute_option->fresh()->product_attribute);
    }
}
