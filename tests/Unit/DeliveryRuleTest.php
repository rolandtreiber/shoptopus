<?php

namespace Tests\Unit;

use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryRuleTest extends TestCase
{
    use RefreshDatabase;

    protected $delivery_rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->delivery_rule = DeliveryRule::factory()->create();
    }

    /** @test */
    public function it_has_a_generated_slug(): void
    {
        $this->assertEquals(Str::slug($this->delivery_rule->delivery_type->name), $this->delivery_rule->slug);
    }

    /** @test */
    public function it_has_a_postcodes_field(): void
    {
        $this->assertNotNull($this->delivery_rule->postcodes);
    }

    /** @test */
    public function it_has_a_min_weight_field(): void
    {
        $this->assertNotNull($this->delivery_rule->min_weight);
    }

    /** @test */
    public function it_has_a_max_weight_field(): void
    {
        $this->assertNotNull($this->delivery_rule->max_weight);
    }

    /** @test */
    public function it_has_a_min_distance_field(): void
    {
        $this->assertNotNull($this->delivery_rule->min_distance);
    }

    /** @test */
    public function it_has_a_max_distance_field(): void
    {
        $this->assertNotNull($this->delivery_rule->max_distance);
    }

    /** @test */
    public function it_has_a_distance_unit_field(): void
    {
        $this->assertEquals('meter', $this->delivery_rule->distance_unit);
    }

    /** @test */
    public function it_has_a_latitude_field(): void
    {
        $this->assertNotNull($this->delivery_rule->lat);
    }

    /** @test */
    public function it_has_a_longitude_field(): void
    {
        $this->assertNotNull($this->delivery_rule->lon);
    }

    /** @test */
    public function it_has_an_enabled_field(): void
    {
        $this->assertTrue($this->delivery_rule->enabled);
    }

    /** @test */
    public function it_belongs_to_a_delivery_type(): void
    {
        $this->assertInstanceOf(DeliveryType::class, $this->delivery_rule->delivery_type);
    }
}
