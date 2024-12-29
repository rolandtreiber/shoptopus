<?php

namespace Tests\PublicApi\Payments\Stripe;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\PaymentTestCase;

/**
 * @group execute-payment-stripe
 */
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
        Mail::fake();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_throws_an_error_if_the_order_status_is_not_awaiting_payments(): void
    {
        $order = Order::factory()->state([
            'status' => OrderStatus::AwaitingPayment
        ])->create(['user_id' => $this->user->id]);

        $total = $order->total_price * 100;

        $data = [
            'provider' => 'stripe',
            'orderId' => $order->id,
            'userId' => $order->user_id,
            'provider_payload' => $this->dummyPaymentIntent($total),
        ];

        $res = $this->signIn($this->user)->sendRequest($data)->json('data.0');

        $this->assertTrue($res['success']);
        $this->assertTrue($res['success']);
        $this->assertEquals(200, $res['status_code']);
        $this->assertEquals('CREATED', $res['status']);
        $this->assertEquals('Stripe', $res['provider']);

        $this->assertDatabaseHas('transaction_stripe', [
            'payment_id' => $res['payment_id'],
            'amount' => $total,
        ]);
    }

    protected function dummyPaymentIntent($total): array
    {
        return [
            'payment_intent_id' => 'MY_DUMMY_INTENT_ID',
            'allowed_source_types' => ['card'],
            'amount' => $total,
            'automatic_payment_methods' => null,
            'canceled_at' => null,
            'cancellation_reason' => null,
            'capture_method' => 'automatic',
            'client_secret' => 'pi_3KxxzjBFieusaDib1qqAQcZA_secret_hbhRbI9Br1di5sSdL0HcAgvol',
            'confirmation_method' => 'automatic',
            'created' => now()->timestamp,
            'currency' => 'gbp',
            'description' => null,
            'id' => 'pi_3KxxzjBFieusaDib1qqAQcZA',
            'last_payment_error' => null,
            'livemode' => false,
            'next_action' => null,
            'next_source_action' => null,
            'object' => 'payment_intent',
            'payment_method' => 'pm_1Kxy03BFieusaDibx2UJBY4M',
            'payment_method_types' => ['card'],
            'processing' => null,
            'receipt_email' => null,
            'setup_future_usage' => null,
            'shipping' => null,
            'source' => null,
            'status' => 'succeeded',
        ];
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.payment.execute'), $data);
    }
}
