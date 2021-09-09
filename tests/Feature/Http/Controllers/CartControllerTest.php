<?php

namespace Tests\Feature\Http\Controllers;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CartController
 */
class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('cart.index'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function update_responds_with()
    {
        $cart = Cart::factory()->create();

        $response = $this->put(route('cart.update', $cart));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function store_responds_with()
    {
        $response = $this->post(route('cart.store'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function destroy_deletes()
    {
        $cart = Cart::factory()->create();
        $cart = Product::factory()->create();

        $response = $this->delete(route('cart.destroy', $cart));

        $this->assertDeleted($cart);
    }
}
