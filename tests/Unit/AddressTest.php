<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    protected $address;

    public function setUp(): void
    {
        parent::setUp();

        $this->address = Address::factory()->create();
    }

    /** @test */
    public function it_has_a_generated_slug()
    {
        $string = optional($this->address->user)->name ?? '';
        $string .= $this->address->name ? ' '.$this->address->name : '';
        $string .= $this->address->town ? ' '.$this->address->town.' ' : '';

        $this->assertEquals(Str::slug(trim($string)), $this->address->slug);
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
        $this->assertNull($this->address->lat);
    }

    /** @test */
    public function it_has_a_longitude_field()
    {
        $this->assertNull($this->address->lon);
    }

    /** @test */
    public function it_has_a_google_maps_url_attribute()
    {
        $this->assertNull($this->address->google_maps_url);

        $this->address->update([
            'lat' => 12.34567,
            'lon' => 12.34567,
        ]);

        $this->address->refresh();

        $this->assertEquals('https://www.google.com/maps/@'.$this->address->lat.','.$this->address->lon.',14z', $this->address->google_maps_url);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->address->user);
    }
}
