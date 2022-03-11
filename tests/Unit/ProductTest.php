<?php

namespace Tests\Unit;

use App\Models\FileContent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tests\TestCase;
use App\Models\Product;
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
        $this->assertEquals(ProductStatus::Provisional, $this->product->status);
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
    public function it_may_have_many_images()
    {
        $this->assertCount(0, $this->product->images());

        FileContent::factory()->create([
            'fileable_type' => get_class($this->product),
            'fileable_id' => $this->product->id
        ]);

        $this->assertInstanceOf(FileContent::class, $this->product->images()->first());
    }
}
