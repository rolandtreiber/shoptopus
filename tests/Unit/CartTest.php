<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
    }

    /** @test */
    public function it_has_an_ip_address_field(): void
    {
        $this->assertNull($this->cart->ip_address);
    }

    /** @test */
    public function it_may_belong_to_a_user(): void
    {
        $this->assertInstanceOf(User::class, $this->cart->fresh()->user);
    }

    /** @test */
    public function it_may_belongs_to_many_products(): void
    {
        $cart = Cart::factory()
            ->hasAttached(Product::factory()->count(3))
            ->create();

        $this->assertCount(3, $cart->products);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $cart->products);
    }
}
