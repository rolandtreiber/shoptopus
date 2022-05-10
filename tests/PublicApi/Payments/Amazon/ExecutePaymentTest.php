<?php

namespace Tests\Api\Payments\Amazon;

use Tests\TestCase;
use Amazon\Pay\API\Client;
use App\Models\Cart\Cart;
use PaymentProviderSeeder;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Customer\Customer;
use App\Models\Competition\Competition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExecutePaymentTest extends TestCase {

    use RefreshDatabase;

    protected $client;

    public function setUp() : void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);

        $this->client = new Client([
            'public_key_id' => config('payment_providers.provider_settings.amazon.sandbox.PUBLIC_KEY_ID'),
            'private_key'   => Storage::get('/amazon/amazon_pay_private_key.pem'),
            'region'        => config('payment_providers.provider_settings.amazon.sandbox.REGION'),
            'sandbox'       => true
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_valid_checkout_session_id()
    {
        $customer = factory(Customer::class)->create();
        $order = factory(Order::class)->create(['customer_id' => $customer->id]);
        factory(Cart::class)->create([
            'user_id' => $customer->user->id,
            'customer_id' => $customer->id
        ]);

        $data = [
            'provider' => 'amazon',
            'uuid' => $order->uuid,
            'provider_payload' => [
                'checkout_session_id' => 'asdsaa'
            ]
        ];

        $res = $this->signIn($customer->user)
            ->sendRequest($data)
            ->json();

        $this->assertEquals(
            "Sorry there was an error processing your payment, our administrators have been informed.",
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

        $customer = factory(Customer::class)->create();
        $order = factory(Order::class)->create(['customer_id' => $customer->id]);

        factory(Cart::class)->create([
            'user_id' => $customer->user->id,
            'customer_id' => $customer->id
        ]);

        factory(OrderItem::class)->create(['order_id' => $order->id]);

        $data = [
            'provider' => 'amazon',
            'uuid' => $order->uuid,
            'provider_payload' => [
                'checkout_session_id' => $this->createAndGetCheckoutSessionId()
            ]
        ];

        $this->signIn($customer->user)
            ->sendRequest($data)
            ->assertJsonStructure([
                'data' => [
                    [
                        'success', 'status_code', 'status', 'payment_id', 'provider'
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @group apiPost
     * @see https://stackoverflow.com/questions/65535379/amazon-pay-sdk-completecheckoutsession-error
     */
    public function it_handles_the_events_correctly()
    {
        $this->markTestSkipped('Skipped test');
        $customer = factory(Customer::class)->create();
        $order = factory(Order::class)->create([
            'customer_id' => $customer->id
        ]);

        factory(Cart::class)->create([
            'user_id' => $customer->user->id,
            'customer_id' => $customer->id
        ]);

        $competition = factory(Competition::class)->create([
            'tickets_available' => 5
        ]);

        $orderItem = factory(OrderItem::class)->create([
            'competition_id' => $competition->id,
            'order_id' => $order->id,
            'quantity' => 2
        ]);

        $availableTickets = $competition->tickets_available;
        $oldCustomerCredits = $customer->credits;
        $this->assertEquals(1, $order->status);
        $this->assertNotNull($customer->cart);


        //$checkoutSessionId = $this->createAndGetCheckoutSessionId();

        $data = [
            'provider' => 'amazon',
            'uuid' => $order->uuid,
            'provider_payload' => [
                'checkout_session_id' => $checkoutSessionId,
            ]
        ];

        $this->signIn($customer->user)
            ->sendRequest($data);

        $competition = Competition::find($orderItem->competition_id);

        $competition->refresh();
        $customer->refresh();
        $order->refresh();

        $this->assertTrue($competition->tickets_available === ($availableTickets - $orderItem->quantity));
        $this->assertTrue($customer->credits === ($oldCustomerCredits +5));
        $this->assertEquals(2, $order->status);
        $this->assertNull($customer->cart);
    }

    protected function createAndGetCheckoutSessionId() : string
    {
        $payload = array(
            'webCheckoutDetails' => array(
                'checkoutReviewReturnUrl' => 'https://localhost/store/checkout_review',
                'checkoutResultReturnUrl' => 'https://localhost/store/checkout_result'
            ),
            'storeId' => config('payment_providers.provider_settings.amazon.sandbox.STORE_ID')
        );
        $header = ['x-amz-pay-idempotency-key' => uniqid()];

        $amazonResponse = $this->client->createCheckoutSession($payload, $header);

        return json_decode($amazonResponse['response'])->checkoutSessionId;
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.payment.execute'), $data);
    }

}
