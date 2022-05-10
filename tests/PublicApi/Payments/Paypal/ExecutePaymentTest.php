<?php

namespace Tests\Api\Payments\Paypal;

use Tests\TestCase;
use App\Models\Cart\Cart;
use PaymentProviderSeeder;
use App\Models\Order\Order;
use App\Models\Customer\Customer;
use App\Models\Competition\Competition;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExecutePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_valid_paypal_order_id_token()
    {
        $customer = factory(Customer::class)->create();
        $order = factory(Order::class)->create(['customer_id' => $customer->id]);
        factory(Cart::class)->create([
            'user_id' => $customer->user->id,
            'customer_id' => $customer->id
        ]);

        $data = [
            'provider' => 'paypal',
            'uuid' => $order->uuid,
            'provider_payload' => [
                [
                    'paypal_order_id_token' => null
                ]
            ]
        ];

        $res = $this->signIn($customer->user)
            ->sendRequest($data)
            ->json();

        $this->assertEquals('Client Authentication failed.', $res['developer_message']);
        $this->assertEquals('Sorry there was an error processing your payment, our administrators have been informed.', $res['user_message']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_order_creation_fails_if_the_user_has_not_approved_the_transaction()
    {
        $customer = factory(Customer::class)->create();
        $competition = factory(Competition::class)->create();
        $order = factory(Order::class)->create(['customer_id' => $customer->id]);
        factory(Cart::class)->create([
            'user_id' => $customer->user->id,
            'customer_id' => $customer->id
        ]);

        $getClientSettingsData = [
            'provider' => 'paypal',
            'data[uuid]' => $order->uuid,
            'data[competitionSlug]' => $competition->slug
        ];

        $res = $this->getJson(route('api.payment.get.settings.public', $getClientSettingsData));

        $paypal_order_id_token = $res->json('data.0.pay_pal_order_creation.result.id');

        $data = [
            'provider' => 'paypal',
            'uuid' => $order->uuid,
            'provider_payload' => [
                [
                    'paypal_order_id_token' => $paypal_order_id_token
                ]
            ]
        ];

        $res = $this->signIn($customer->user)
            ->sendRequest($data);

        $res->assertStatus(500);

        $error_response = json_decode($res->json('developer_message'));

        $this->assertEquals('UNPROCESSABLE_ENTITY', $error_response->name);
        $this->assertEquals('ORDER_NOT_APPROVED', $error_response->details[0]->issue);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.payment.execute'), $data);
    }
}
