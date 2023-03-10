<?php

namespace Tests\PublicApi\Payments\Paypal;

use App\Models\Order;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PaymentTestCase;

class GetClientSettingsTest extends PaymentTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_can_make_a_request_to_get_the_client_settings_and_an_id_is_being_returned()
    {
        $order = Order::factory()->create();

        $data = [
            'provider' => 'paypal',
            'orderId' => $order->id,
        ];

        $res = $this->sendRequest($data);

        $res->assertSuccessful();

        $this->assertNotNull($res->json('data.0.pay_pal_order_creation.result.id'));

        $res->assertJsonStructure([
            'data' => [
                [
                    'client_id',
                    'pay_pal_order_creation' => [
                        'statusCode',
                        'result' => [
                            'id',
                            'intent',
                            'status',
                            'purchase_units' => [
                                [
                                    'reference_id',
                                    'amount' => [
                                        'currency_code',
                                        'value',
                                    ],
                                    'payee' => [
                                        'email_address',
                                        'merchant_id',
                                    ],
                                ],
                            ],
                            'create_time',
                            'links' => [
                                [
                                    'href',
                                    'rel',
                                    'method',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.payment.get.settings.public', $data));
    }
}
