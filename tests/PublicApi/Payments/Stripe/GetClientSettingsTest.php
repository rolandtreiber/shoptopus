<?php

namespace Tests\PublicApi\Payments\Stripe;

use App\Models\Order;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PaymentTestCase;

class GetClientSettingsTest extends PaymentTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);
    }

    /**
     * @test
     * @group apiGet
     */
    public function stripe_can_make_a_request_to_get_the_client_secret_and_publishable_key()
    {
        $order = Order::factory()->create();

        $data = [
            'provider' => 'stripe',
            'orderId' => $order->id,
        ];

        $res = $this->sendRequest($data);

        $res->assertSuccessful();

        $res->assertJsonStructure([
            'data' => [
                [
                    'publishableKey',
                    'clientSecret',
                    'order_total',
                ],
            ],
        ]);

        $this->assertEquals($order->total_price * 100, $res->json('data.0.order_total'));
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.payment.get.settings.public', $data));
    }
}
