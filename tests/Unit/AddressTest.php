<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    protected $address;

    public function setUp() : void
    {
        parent::setUp();

        $this->address = Address::factory()->create();
    }

    /** @test */
    public function it_has_an_address_line_1_field()
    {
        $this->assertNotNull($this->address->address_line_1);
    }

    /** @test */
    public function it_has_a_town_field()
    {
        $this->assertNotNull($this->address->town);
    }

    /** @test */
    public function it_has_a_post_code_field()
    {
        $this->assertNotNull($this->address->post_code);
    }

    /** @test */
    public function it_has_an_country_field()
    {
        $this->assertEquals('UK', $this->address->country);
    }

    /** @test */
    public function it_has_a_name_field()
    {
        $this->assertNull($this->address->name);
    }

    /** @test */
    public function it_has_an_address_line_2_field()
    {
        $this->assertNull($this->address->address_line_2);
    }

    /** @test */
    public function it_has_a_latitude_field()
    {
        $this->assertNotNull($this->address->lat);
    }

    /** @test */
    public function it_has_a_longitude_field()
    {
        $this->assertNotNull($this->address->lon);
    }

    /** @test */
    public function it_may_belong_to_a_user()
    {
        $this->assertNotNull($this->address->user);

        $this->address->update(['user_id' => User::factory()->create()->id]);

        $this->assertInstanceOf(User::class, $this->address->fresh()->user);
    }
}
