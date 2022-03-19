<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use Illuminate\Support\Str;
use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group delivery_type
 */
class DeliveryTypeTest extends TestCase
{
    use RefreshDatabase;

    protected $delivery_type;

    public function setUp() : void
    {
        parent::setUp();

        $this->delivery_type = DeliveryType::factory()->create();
    }

    /** @test */
    public function it_has_an_name_field()
    {
        $this->assertNotNull($this->delivery_type->name);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_name()
    {
        $this->assertEquals(Str::slug($this->delivery_type->name), $this->delivery_type->slug);
    }

    /** @test */
    function it_returns_a_translated_name()
    {
        $this->delivery_type
            ->setTranslation('name', 'en', 'english translation')
            ->setTranslation('name', 'de', 'german translation')
            ->save();

        $this->assertEquals($this->delivery_type->name, 'english translation');

        app()->setLocale('de');

        $this->assertEquals($this->delivery_type->name, 'german translation');
    }

    /** @test */
    public function it_has_a_description_field()
    {
        $this->assertNotNull($this->delivery_type->description);
    }

    /** @test */
    function it_returns_a_translated_description()
    {
        $this->delivery_type
            ->setTranslation('description', 'en', 'english translation')
            ->setTranslation('description', 'de', 'german translation')
            ->save();

        $this->assertEquals($this->delivery_type->description, 'english translation');

        app()->setLocale('de');

        $this->assertEquals($this->delivery_type->description, 'german translation');
    }

    /** @test */
    public function it_has_a_price_field()
    {
        $this->assertNotNull($this->delivery_type->price);
    }

    /** @test */
    public function it_has_an_enabled_field()
    {
        $this->assertTrue($this->delivery_type->enabled);
    }

    /** @test */
    public function it_has_an_enabled_by_default_on_creation_field()
    {
        $this->assertTrue($this->delivery_type->enabled_by_default_on_creation);
    }

    /** @test */
    public function it_may_have_many_delivery_rules()
    {
        $this->assertCount(0, $this->delivery_type->deliveryRules);

        DeliveryRule::factory()->count(2)->create(['delivery_type_id' => $this->delivery_type->id]);

        $this->assertCount(2, $this->delivery_type->refresh()->deliveryRules);

        $this->assertInstanceOf(DeliveryRule::class, $this->delivery_type->deliveryRules[0]);
    }

    /** @test */
    public function it_may_have_many_orders()
    {
        $this->assertCount(0, $this->delivery_type->orders);

        Order::factory()->count(2)->create(['delivery_type_id' => $this->delivery_type->id]);

        $this->assertCount(2, $this->delivery_type->refresh()->orders);

        $this->assertInstanceOf(Order::class, $this->delivery_type->orders[0]);
    }
}
