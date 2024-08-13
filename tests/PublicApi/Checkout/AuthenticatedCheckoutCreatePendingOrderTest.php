<?php

namespace PublicApi\Checkout;

use App\Enums\DiscountType;
use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DeliveryType;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group authenticated-checkout-create-pending-order
 */
class AuthenticatedCheckoutCreatePendingOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    /**
     * @test
     */
    public function authenticated_checkout_works_when_all_information_are_provided_and_correct()
    {
        $products = Product::factory()
            ->count(2)
            ->state([
                'stock' => 20,
                'price' => 5
            ])
            ->create();
        $productVariants = ProductVariant::factory()
            ->count(2)
            ->state([
                'stock' => 20,
                'price' => 10
            ])
            ->create();
        /** @var DiscountRule $discountRule1 */
        $discountRule1 = DiscountRule::factory()->state([
            'type' => DiscountType::Amount,
            'amount' => 2.5,
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay()
        ])->create();
        /** @var DiscountRule $discountRule1 */
        $discountRule2 = DiscountRule::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 5,
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay()
        ])->create();
        $discountRule1->products()->attach($products[0]);
        $discountRule2->products()->attach($products[1]);

        $cart = new Cart();
        $cart->user_id = $this->user->id;
        $cart->save();
        $cart->products()->attach($products[0], ['quantity' => 2]); // 2x5 = 10
        $cart->products()->attach($products[1], ['quantity' => 3]); // 3x5 = 15
        $cart->products()->attach($productVariants[0]->product_id, [
            'quantity' => 3,
            'product_variant_id' => $productVariants[0]->id
        ]); // 3x10 = 30
        $cart->products()->attach($productVariants[1]->product_id, [
            'quantity' => 5,
            'product_variant_id' => $productVariants[1]->id
        ]); // 5x10 = 50
        $deliveryType = DeliveryType::factory()->state(['price' => 3])->create();
        $voucherCode = VoucherCode::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 8,
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay()
        ])->create();
        $address = Address::factory()->state(['user_id' => $this->user->id])->create();

        // The order total without discounts:
        // 10+15+30+50+3 = 108 (discounts don't apply to delivery, so it is 105)
        // Discount rule of 2.5 applied to product 1 (price: 5, quantity: 2) -> discount = 5
        // Discount rule of 5% applied to product 2 (price: 5, quantity: 3) -> discount = 0.75
        // Total discount applied = 5.75
        // Total payable should be 99.25 (+3 delivery) at this point
        // ... however we are also applying a voucher code as per the following:
        // Voucher code applied: 8%: 99.25 * (0.08) -> discount = 7.94
        // Total payable with all included: 91.31 + 3 = 94.31

        $orderId = $this->signIn($this->user)->sendRequest([
            'cart_id' => $cart->id,
            'address_id' => $address->id,
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => false,
            "voucher_code" => $voucherCode->code
        ])->json('data.0.order_id');

        $this->assertDatabaseHas("orders", [
            "id" => $orderId,
            "total_price" => 94.31,
            "status" => OrderStatus::AwaitingPayment
        ]);

        $this->assertDatabaseCount('order_product', 4);
        $this->assertCount(0, $cart->refresh()->products);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $products[0]->id,
            'amount' =>  2
        ]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $products[1]->id,
            'amount' =>  3
        ]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $productVariants[0]->product_id,
            'product_variant_id' => $productVariants[0]->id,
            'amount' =>  3
        ]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $productVariants[1]->product_id,
            'product_variant_id' => $productVariants[1]->id,
            'amount' =>  5
        ]);
    }

    /**
     * @test
     */
    public function authenticated_checkout_order_price_calculated_correctly_when_discount_rule_is_inactive()
    {
        $products = Product::factory()
            ->count(2)
            ->state([
                'stock' => 20,
                'price' => 5
            ])
            ->create();
        $productVariants = ProductVariant::factory()
            ->count(2)
            ->state([
                'stock' => 20,
                'price' => 10
            ])
            ->create();
        /** @var DiscountRule $discountRule1 */
        $discountRule1 = DiscountRule::factory()->state([
            'type' => DiscountType::Amount,
            'amount' => 2.5,
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay()
        ])->create();
        /** @var DiscountRule $discountRule1 */
        $discountRule2 = DiscountRule::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 5,
            'enabled' => 1,
            'valid_from' => Carbon::now()->addDay(),
            'valid_until' => Carbon::now()->addDays(2) // This one hasn't started yet, so should not be applied
        ])->create();
        $discountRule1->products()->attach($products[0]);
        $discountRule2->products()->attach($products[1]);
        $address = Address::factory()->state(['user_id' => $this->user->id])->create();

        $cart = new Cart();
        $cart->user_id = $this->user->id;
        $cart->save();
        $cart->products()->attach($products[0], ['quantity' => 2]); // 2x5 = 10
        $cart->products()->attach($products[1], ['quantity' => 3]); // 3x5 = 15
        $cart->products()->attach($productVariants[0]->product_id, [
            'quantity' => 3,
            'product_variant_id' => $productVariants[0]->id
        ]); // 3x10 = 30
        $cart->products()->attach($productVariants[1]->product_id, [
            'quantity' => 5,
            'product_variant_id' => $productVariants[1]->id
        ]); // 5x10 = 50
        $deliveryType = DeliveryType::factory()->state(['price' => 3])->create();
        $voucherCode = VoucherCode::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 8,
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay()
        ])->create();

        // The order total without discounts:
        // 10+15+30+50+3 = 108 (discount doesn't apply to delivery, which is 3)
        // Discount rule of 2.5 applied to product 1 (price: 5, quantity: 2) -> discount = 5
        // Total discount applied = 5
        // Total payable should be 100 at this point
        // ... however we are also applying a voucher code as per the following:
        // Voucher code applied: 8%: 100 * (0.08) -> discount = 8
        // Total payable with all included: 92+3 = 95

        $orderId = $this->signIn($this->user)->sendRequest([
            'cart_id' => $cart->id,
            'address_id' => $address->id,
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => false,
            "voucher_code" => $voucherCode->code
        ])->json('data.0.order_id');

        $this->assertDatabaseHas("orders", [
            "id" => $orderId,
            "total_price" => 95
        ]);
    }

    /**
     * @test
     */
    public function authenticated_checkout_order_price_calculated_correctly_when_voucher_code_is_inactive()
    {
        $products = Product::factory()
            ->count(2)
            ->state([
                'stock' => 20,
                'price' => 5
            ])
            ->create();
        $productVariants = ProductVariant::factory()
            ->count(2)
            ->state([
                'stock' => 20,
                'price' => 10
            ])
            ->create();
        /** @var DiscountRule $discountRule1 */
        $discountRule1 = DiscountRule::factory()->state([
            'type' => DiscountType::Amount,
            'amount' => 2.5,
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay()
        ])->create();
        /** @var DiscountRule $discountRule1 */
        $discountRule2 = DiscountRule::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 5,
            'enabled' => 1,
            'valid_from' => Carbon::now()->addDay(),
            'valid_until' => Carbon::now()->addDays(2) // This one hasn't started yet, so should not be applied
        ])->create();
        $discountRule1->products()->attach($products[0]);
        $discountRule2->products()->attach($products[1]);
        $address = Address::factory()->state(['user_id' => $this->user->id])->create();

        $cart = new Cart();
        $cart->user_id = $this->user->id;
        $cart->save();
        $cart->products()->attach($products[0], ['quantity' => 2]); // 2x5 = 10
        $cart->products()->attach($products[1], ['quantity' => 3]); // 3x5 = 15
        $cart->products()->attach($productVariants[0]->product_id, [
            'quantity' => 3,
            'product_variant_id' => $productVariants[0]->id
        ]); // 3x10 = 30
        $cart->products()->attach($productVariants[1]->product_id, [
            'quantity' => 5,
            'product_variant_id' => $productVariants[1]->id
        ]); // 5x10 = 50
        $deliveryType = DeliveryType::factory()->state(['price' => 3])->create();
        $voucherCode = VoucherCode::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 8,
            'enabled' => 1,
            'valid_from' => Carbon::now()->addDay(),
            'valid_until' => Carbon::now()->addDays(2)
        ])->create();
        $address = Address::factory()->state(['user_id' => $this->user->id])->create();

        // The order total without discounts:
        // 10+15+30+50+3 = 108
        // Discount rule of 2.5 applied to product 1 (price: 5, quantity: 2) -> discount = 5
        // Total discount applied = 5
        // Total payable should be 103 at this point
        // ... however we are also applying a voucher code as per the following:
        // Total payable with all included: 103

        $orderId = $this->signIn($this->user)->sendRequest([
            'cart_id' => $cart->id,
            'address_id' => $address->id,
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => false,
            "voucher_code" => $voucherCode->code
        ])->json('data.0.order_id');

        $this->assertDatabaseHas("orders", [
            "id" => $orderId,
            "total_price" => 103
        ]);
    }

    /**
     * @test
     */
    public function authenticated_checkout_attempting_to_checkout_empty_cart_fails_gracefully()
    {
        $cart = new Cart();
        $cart->user_id = $this->user->id;
        $cart->save();
        $deliveryType = DeliveryType::factory()->state(['price' => 3])->create();
        $voucherCode = VoucherCode::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 8,
            'enabled' => 1,
            'valid_from' => Carbon::now()->addDay(),
            'valid_until' => Carbon::now()->addDays(2)
        ])->create();
        $address = Address::factory()->state(['user_id' => $this->user->id])->create();

        $res = $this->signIn($this->user)->sendRequest([
            'cart_id' => $cart->id,
            'address_id' => $address->id,
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => false,
            "voucher_code" => $voucherCode->code
        ])->json('developer_message');

        $this->assertEquals("Empty cart", $res);
    }

    protected function sendRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.create.pending-order', $data));
    }

}
