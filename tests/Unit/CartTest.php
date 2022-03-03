<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected $cart;

    public function setUp() : void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
    }

    /** @test */
    public function it_has_an_ip_address_field()
    {
        $this->assertNull($this->cart->ip_address);
    }

    /** @test */
    public function it_may_belong_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->cart->fresh()->user);
    }

    /** @test */
    public function it_may_belongs_to_many_products()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->cart->fresh()->products);
    }
}
