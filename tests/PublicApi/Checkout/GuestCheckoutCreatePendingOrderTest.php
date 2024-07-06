<?php

namespace PublicApi\Checkout;

use App\Enums\DiscountType;
use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DeliveryType;
use App\Models\DiscountRule;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group guest-checkout-create-pending-order
 */
class GuestCheckoutCreatePendingOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guest_checkout_works_when_all_information_are_provided_and_correct()
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
        // 10+15+30+50+3 = 108
        // Discount rule of 2.5 applied to product 1 (price: 5, quantity: 2) -> discount = 5
        // Discount rule of 5% applied to product 2 (price: 5, quantity: 3) -> discount = 0.75
        // Total discount applied = 5.75
        // Total payable should be 102.25 at this point
        // ... however we are also applying a voucher code as per the following:
        // Voucher code applied: 8%: 102.25 * (0.08) -> discount = 8.18
        // Total payable with all included: 94.07

        $orderId = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'johnsmith@email.com'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
            "voucher_code_id" => $voucherCode->id
        ])->json('data.0.order_id');

        $this->assertDatabaseHas("orders", [
            "id" => $orderId,
            "total_price" => 94.07,
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

        $user = User::find(Order::find($orderId)->user_id);
        $this->assertStringContainsString($user->refresh()->client_ref, $user->refresh()->email);
        $this->assertEquals($user->temporary, 1);

    }

    /**
     * @test
     */
    public function guest_checkout_order_price_calculated_correctly_when_discount_rule_is_inactive()
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

        $cart = new Cart();
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
        // 10+15+30+50+3 = 108
        // Discount rule of 2.5 applied to product 1 (price: 5, quantity: 2) -> discount = 5
        // Total discount applied = 5
        // Total payable should be 103 at this point
        // ... however we are also applying a voucher code as per the following:
        // Voucher code applied: 8%: 103 * (0.08) -> discount = 8.24
        // Total payable with all included: 94.76

        $orderId = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'johnsmith@email.com'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
            "voucher_code_id" => $voucherCode->id
        ])->json('data.0.order_id');

        $this->assertDatabaseHas("orders", [
            "id" => $orderId,
            "total_price" => 94.76
        ]);
    }

    /**
     * @test
     */
    public function guest_checkout_order_price_calculated_correctly_when_voucher_code_is_inactive()
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

        $cart = new Cart();
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

        // The order total without discounts:
        // 10+15+30+50+3 = 108
        // Discount rule of 2.5 applied to product 1 (price: 5, quantity: 2) -> discount = 5
        // Total discount applied = 5
        // Total payable should be 103 at this point
        // ... however we are also applying a voucher code as per the following:
        // Total payable with all included: 103

        $orderId = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'johnsmith@email.com'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
            "voucher_code_id" => $voucherCode->id
        ])->json('data.0.order_id');

        $this->assertDatabaseHas("orders", [
            "id" => $orderId,
            "total_price" => 103
        ]);
    }

    /**
     * @test
     */
    public function guest_checkout_user_object_validation_object_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No user details present at guest checkout", $errorMsg);
    }


    /**
     * @test
     */
    public function guest_checkout_user_object_validation_email_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No email field present in the user object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_user_object_validation_email_at_sign_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'clearlyInvalid.email'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: Invalid user email", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_user_object_validation_email_dot_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'clearly@invalidemail'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: Invalid user email", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_user_object_validation_email_too_short_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 's@.'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: Invalid user email", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_user_object_validation_first_name_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'last_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No first_name field present in the user object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_user_object_validation_last_name_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No last_name field present in the user object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_address_object_validation_object_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No address details present at guest checkout", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_address_object_validation_town_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            "delivery_type_id" => $deliveryType->id,
            'address' => [
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No town field present in the address object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_address_object_validation_address_line_1_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            "delivery_type_id" => $deliveryType->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No address_line_1 field present in the address object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_address_object_validation_post_code_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            "delivery_type_id" => $deliveryType->id,
            'address' => [
                "town" => "Bristol",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No post_code field present in the address object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_address_object_validation_lat_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            "delivery_type_id" => $deliveryType->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lon" => -2.6090162,
                "country" => "UK"
            ],
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No lat field present in the address object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_address_object_validation_lon_missing_returns_correct_message()
    {
        $product = Product::factory()->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product, ['quantity' => 2]);
        $deliveryType = DeliveryType::factory()->create();

        $errorMsg = $this->sendRequest([
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'testemail@test.com'
            ],
            'cart_id' => $cart->id,
            "delivery_type_id" => $deliveryType->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "country" => "UK"
            ],
            "guest_checkout" => true,
        ])->json('developer_message');

        $this->assertEquals("Checkout error: No lon field present in the address object", $errorMsg);
    }

    /**
     * @test
     */
    public function guest_checkout_attempting_to_checkout_empty_cart_fails_gracefully()
    {
        $cart = new Cart();
        $cart->save();
        $deliveryType = DeliveryType::factory()->state(['price' => 3])->create();
        $voucherCode = VoucherCode::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 8,
            'enabled' => 1,
            'valid_from' => Carbon::now()->addDay(),
            'valid_until' => Carbon::now()->addDays(2)
        ])->create();

        $res = $this->sendRequest([
            'cart_id' => $cart->id,
            'address' => [
                "town" => "Bristol",
                "post_code" => "BS10 6RX",
                "address_line_1" => "6. Forest Drive",
                "lat" => 51.510041,
                "country" => "UK"
            ],
            "delivery_type_id" => $deliveryType->id,
            "guest_checkout" => true,
            "voucher_code_id" => $voucherCode->id
        ])->json('developer_message');

        $this->assertEquals("Empty cart", $res);
    }


    protected function sendRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.create.pending-order', $data));
    }

}
