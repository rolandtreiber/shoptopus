<?php

namespace Tests\PublicApi\Payments;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PaymentTestCase;

class ExecutePaymentTest extends PaymentTestCase
{
    use RefreshDatabase;

    protected $cart;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);

        $this->cart = Cart::factory()->create();
        $this->user = $this->cart->user;
    }
    
    /**
     * @test
     *
     * @group apiPost
     */
    public function unauthorised_users_are_not_allowed_to_execute_payments(): void
    {
        $res = $this->signIn()->sendRequest()->json();

        $this->assertEquals('This action is unauthorized.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_throws_an_error_if_the_payment_gateway_is_not_supported(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'provider' => 'some provider',
            'orderId' => $order->id,
        ];

        $this->signIn($this->user)->sendRequest($data)->assertJsonValidationErrors(['provider']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_throws_an_error_if_the_order_status_is_not_awaiting_payment(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
        ]);

        $data = [
            'provider' => 'stripe',
            'orderId' => $order->id,
        ];

        $res = $this->signIn($this->user)->sendRequest($data)->json();

        $this->assertEquals('This action is unauthorized.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.payment.execute'), $data);
    }
}
