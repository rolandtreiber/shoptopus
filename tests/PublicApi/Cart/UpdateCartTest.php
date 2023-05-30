<?php

namespace Tests\PublicApi\Cart;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCartTest extends TestCase
{
    use RefreshDatabase;

    protected $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
    }

    /**
     * @test
     *
     * @group apiPatch
     */
    public function unauthorised_users_are_not_allowed_to_update_carts(): void
    {
        $data = Cart::factory()->raw();

        $unAuthenticatedRes = $this->sendRequest($data)->json();

        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);

        $unAuthorisedRes = $this->signIn()->sendRequest($data)->json();

        $this->assertEquals('This action is unauthorized.', $unAuthorisedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthorisedRes['user_message']);
    }

    /**
     * @test
     *
     * @group apiPatch
     */
    public function authorised_users_can_update_carts(): void
    {
        $user = User::factory()->create();

        $this->cart->update(['user_id' => $user->id]);

        $data = [
            'ip_address' => '127.0.0.1',
        ];

        $this->signIn($user)->sendRequest($data)->assertOk();

        $this->assertDatabaseHas('carts', $data);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('api.cart.update', ['id' => $this->cart->id]), $data);
    }
}
