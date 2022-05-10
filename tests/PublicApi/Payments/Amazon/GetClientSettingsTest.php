<?php

namespace Tests\Api\Payments\Amazon;

use Tests\TestCase;
use PaymentProviderSeeder;
use App\Models\Order\Order;
use App\Models\Competition\Competition;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetClientSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
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
        $order = factory(Order::class)->create();
        $competition = factory(Competition::class)->create();

        $data = [
            'provider' => 'amazon',
            'data[uuid]' => $order->uuid,
            'data[competitionSlug]' => $competition->slug
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
                                'checkoutResultReturnUrl'
                            ],
                            'paymentDetails' => [
                                'paymentIntent',
                                'chargeAmount' => [
                                    'amount',
                                    'currencyCode'
                                ]
                            ],
                            'merchantMetadata' => [
                                'merchantReferenceId',
                                'merchantStoreName',
                                'noteToBuyer'
                            ],
                            'storeId',
                        ],
                        'signature'
                    ]
                ]
            ]
        ]);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.payment.get.settings.public', $data));
    }
}
