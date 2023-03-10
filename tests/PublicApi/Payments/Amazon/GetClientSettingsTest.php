<?php

namespace Tests\PublicApi\Payments\Amazon;

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
    public function it_can_make_a_request_to_get_the_client_settings_and_returns_the_necessary_data()
    {
        $order = Order::factory()->create();

        $data = [
            'provider' => 'amazon',
            'orderId' => $order->id,
        ];

        $res = $this->sendRequest($data);

        $res->assertSuccessful();

        $res->assertJsonStructure([
            'data' => [
                [
                    'merchantId',
                    'publicKeyId',
                    'ledgerCurrency',
                    'checkoutLanguage',
                    'productType',
                    'placement',
                    'buttonColor',
                    'createCheckoutSessionConfig' => [
                        'payloadJSON' => [
                            'webCheckoutDetails' => [
                                'checkoutMode',
                                'checkoutResultReturnUrl',
                            ],
                            'paymentDetails' => [
                                'paymentIntent',
                                'chargeAmount' => [
                                    'amount',
                                    'currencyCode',
                                ],
                            ],
                            'merchantMetadata' => [
                                'merchantReferenceId',
                                'merchantStoreName',
                                'noteToBuyer',
                            ],
                            'storeId',
                        ],
                        'signature',
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
