<?php

namespace Tests\PublicApi\Payments\Amazon;

use Amazon\Pay\API\Client;
use App\Models\Order;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\PaymentTestCase;

class ExecutePaymentTest extends PaymentTestCase
{
    use RefreshDatabase;

    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);

        $this->client = new Client([
            'public_key_id' => config('payment_providers.provider_settings.amazon.sandbox.PUBLIC_KEY_ID'),
            'private_key' => Storage::get('/amazon/amazon_pay_private_key.pem'),
            'region' => config('payment_providers.provider_settings.amazon.sandbox.REGION'),
            'sandbox' => true,
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_valid_checkout_session_id()
    {
        $order = Order::factory()->create();

        $data = [
            'provider' => 'amazon',
            'orderId' => $order->id,
            'provider_payload' => [
                'checkout_session_id' => 'asdsaa',
            ],
        ];

        $res = $this->signIn($order->user)->sendRequest($data)->json();

        $this->assertEquals(
            'Sorry there was an error processing your payment, our administrators have been informed.',
            $res['user_message']
        );

        $this->assertEquals(
            "You submitted an invalid value for at least one of the parameters of your API call.\nFor details, check the message element in the API response",
            $res['developer_message']
        );
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_returns_a_success_response()
    {
        $this->markTestSkipped('Skipped test');

        $order = Order::factory()->create();

        $data = [
            'provider' => 'amazon',
            'orderId' => $order->id,
            'provider_payload' => [
                'checkout_session_id' => $this->createAndGetCheckoutSessionId(),
            ],
        ];

        $this->signIn($order->user)
            ->sendRequest($data)
            ->assertJsonStructure([
                'data' => [
                    [
                        'success', 'status_code', 'status', 'payment_id', 'provider',
                    ],
                ],
            ]);
    }

    protected function createAndGetCheckoutSessionId(): string
    {
        $payload = [
            'webCheckoutDetails' => [
                'checkoutReviewReturnUrl' => 'https://localhost/store/checkout_review',
                'checkoutResultReturnUrl' => 'https://localhost/store/checkout_result',
            ],
            'storeId' => config('payment_providers.provider_settings.amazon.sandbox.STORE_ID'),
        ];
        $header = ['x-amz-pay-idempotency-key' => uniqid()];

        $amazonResponse = $this->client->createCheckoutSession($payload, $header);

        return json_decode($amazonResponse['response'])->checkoutSessionId;
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.payment.execute'), $data);
    }
}
