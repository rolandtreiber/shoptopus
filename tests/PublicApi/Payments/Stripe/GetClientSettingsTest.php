<?php

namespace Tests\PublicApi\Payments\Stripe;

use App\Enums\DiscountType;
use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\Product;
use App\Models\VoucherCode;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\PaymentTestCase;

/**
 *  @group get-payment-provider-settings-stripe
 */
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
     *
     * @group apiGet
     */
    public function stripe_can_make_a_request_to_get_the_client_secret_and_publishable_key(): void
    {
        $cart = Cart::factory()->create();
        $products = Product::factory()->state([
            'status' => ProductStatus::Active,
            'stock' => 50,
            'price' => 5
        ])->count(2)->create();
        $cart->products()->attach($products[0], ['quantity' => 2]);
        $cart->products()->attach($products[1], ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->state([
            'enabled' => 1,
            'price' => 3
        ])->create();
        $voucherCode = VoucherCode::factory()->state([
            'type' => DiscountType::Amount,
            'amount' => 1,
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay()
        ])->create();

        $data = [
            'provider' => 'stripe',
            'cartId' => $cart->id,
            'voucherCode' => $voucherCode->code,
            'deliveryTypeId' => $deliveryType->id
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

        $this->assertEquals(($cart->getTotals($voucherCode)['total_price'] + $deliveryType->price) * 100, $res->json('data.0.order_total'));
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.payment.get.settings.public', $data));
    }
}
