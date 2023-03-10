<?php

namespace Tests\PublicApi\Payments\Paypal;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);

        $this->cart = Cart::factory()->create();
        $this->user = $this->cart->user;
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_valid_paypal_order_id_token()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'provider' => 'paypal',
            'orderId' => $order->id,
            'provider_payload' => [
                [
                    'paypal_order_id_token' => null,
                ],
            ],
        ];

        $res = $this->signIn($order->user)->sendRequest($data)->json();

        $this->assertEquals('Client Authentication failed.', $res['developer_message']);
        $this->assertEquals('Sorry there was an error processing your payment, our administrators have been informed.', $res['user_message']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_order_creation_fails_if_the_user_has_not_approved_the_transaction()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $getClientSettingsData = [
            'provider' => 'paypal',
            'orderId' => $order->id,
        ];

        $res = $this->getJson(route('api.payment.get.settings.public', $getClientSettingsData));
        $paypal_order_id = $res->json('data.0.pay_pal_order_creation.result.id');

        $data = [
            'provider' => 'paypal',
            'orderId' => $order->id,
            'provider_payload' => [
                [
                    'paypal_order_id_token' => $paypal_order_id,
                ],
            ],
        ];

        $res = $this->signIn($this->user)->sendRequest($data);

        $res->assertStatus(500);

        $error_response = json_decode($res->json('developer_message'));

        $this->assertEquals('UNPROCESSABLE_ENTITY', $error_response->name);
        $this->assertEquals('ORDER_NOT_APPROVED', $error_response->details[0]->issue);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.payment.execute'), $data);
    }
}
