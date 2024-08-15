<?php

namespace PublicApi\Cart;

use App\Enums\DiscountType;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DeliveryType;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group price-calculation-scenarios
 */
class PriceCalculationScenariosTest extends TestCase
{
    use RefreshDatabase;

    private Cart $cart;
    private ProductCategory $category1;
    private ProductCategory $category2;
    private Product $productWithoutCategory;
    private Product $product1WithCategory1;
    private Product $product2WithCategory1;
    private Product $productWithCategory2;
    private ProductVariant $productVariant;
    private VoucherCode $voucherCode10PercentOff;
    private VoucherCode $voucherCode2point5Off;
    private DiscountRule $discountRule5PercentOffProductDirectly;
    private DiscountRule $discountRule3PercentOffCategory1;
    private DiscountRule $discountRule1point2OffCategory2;
    private DiscountRule $discountRule3point3PercentOffProduct1Category1;
    private User $user;
    private Address $address;
    private DeliveryType $deliveryType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->address = Address::factory()->state([
            'user_id' => $this->user->id
        ])->create();
        $this->cart = Cart::factory()->state([
            'user_id' => $this->user->id
        ])->create();
        $this->deliveryType = DeliveryType::factory()->state([
            'price' => 5,
            'enabled' => 1
        ])->create();
        $this->category1 = ProductCategory::factory()->state([
            'enabled' => 1
        ])->create();
        $this->category2 = ProductCategory::factory()->state([
            'enabled' => 1
        ])->create();
        // Creating the products
        $this->productWithoutCategory = Product::factory()->state([
            'price' => 10,
            'name' => 'Product Without Any Category',
            'status' => ProductStatus::Active,
            'stock' => 100
        ])->create();
        ($this->product1WithCategory1 = Product::factory()->state([
            'price' => 15,
            'name' => 'Product 1 With Category 1',
            'status' => ProductStatus::Active,
            'stock' => 100
        ])->create())->product_categories()->attach($this->category1->id);
        ($this->product2WithCategory1 = Product::factory()->state([
            'price' => 21,
            'name' => 'Product 2 With Category 1',
            'status' => ProductStatus::Active,
            'stock' => 100
        ])->create())->product_categories()->attach($this->category1->id);
        ($this->productWithCategory2 = Product::factory()->state([
            'price' => 25,
            'name' => 'Product With Category 2',
            'status' => ProductStatus::Active,
            'stock' => 100
        ])->create())->product_categories()->attach($this->category2->id);
        $this->productVariant = ProductVariant::factory()->state([
            'product_id' => $this->productWithoutCategory,
            'price' => 9,
            'enabled' => 1,
            'stock' => 100
        ])->create();

        $yesterday = Carbon::now()->subDay();
        $tomorrow = Carbon::now()->addDay();

        // Creating the voucher codes
        $this->voucherCode10PercentOff = VoucherCode::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 10,
            'enabled' => 1,
            'valid_from' => $yesterday,
            'valid_until' => $tomorrow,
        ])->create();
        $this->voucherCode2point5Off = VoucherCode::factory()->state([
            'type' => DiscountType::Amount,
            'amount' => 2.5,
            'enabled' => 1,
            'valid_from' => $yesterday,
            'valid_until' => $tomorrow,
        ])->create();
        ($this->discountRule5PercentOffProductDirectly = DiscountRule::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 5,
            'enabled' => 1,
            'valid_from' => $yesterday,
            'valid_until' => $tomorrow,
            'name' => '5% off'
        ])->create())->products()->attach($this->productWithoutCategory->id);
        ($this->discountRule3PercentOffCategory1 = DiscountRule::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 3,
            'enabled' => 1,
            'valid_from' => $yesterday,
            'valid_until' => $tomorrow,
            'name' => '3% off'
        ])->create())->categories()->attach($this->category1->id);
        ($this->discountRule1point2OffCategory2 = DiscountRule::factory()->state([
            'type' => DiscountType::Amount,
            'amount' => 1.2,
            'enabled' => 1,
            'valid_from' => $yesterday,
            'valid_until' => $tomorrow,
            'name' => '1.2 off'
        ])->create())->categories()->attach($this->category2->id);
        ($this->discountRule3point3PercentOffProduct1Category1 = DiscountRule::factory()->state([
            'type' => DiscountType::Percentage,
            'amount' => 3.3,
            'enabled' => 1,
            'valid_from' => $yesterday,
            'valid_until' => $tomorrow,
            'name' => '3.3% off'
        ])->create())->products()->attach($this->product1WithCategory1->id);

        // Adding products to the cart
        $this->cart->products()->attach(
            $this->productWithoutCategory->id,
            [
                'quantity' => 2,
                'product_variant_id' => $this->productVariant->id
            ]
        );
        $this->cart->products()->attach(
            $this->productWithoutCategory->id,
            [
                'quantity' => 3,
            ]
        );
        $this->cart->products()->attach(
            $this->product1WithCategory1->id,
            [
                'quantity' => 4
            ]
        );
        $this->cart->products()->attach(
            $this->product2WithCategory1->id,
            [
                'quantity' => 5
            ]
        );
        $this->cart->products()->attach(
            $this->productWithCategory2->id,
            [
                'quantity' => 7
            ]
        );

        /**
         * --------- Cart price calculation without discount applied ---------
         * Product Without Any Category ($this->productWithoutCategory)
         *   No product variant
         *   - Price: 10
         *   - Quantity: 3
         *   --------------
         *   Total: 30
         *
         *   Product Variant Specified
         *   - Price: 9 (specified by the variant)
         *   - Quantity: 2
         *   --------------
         *   Total: 18
         *
         * Product 1 With Category 1 ($this->product1WithCategory1)
         *   - Price: 15
         *   - Quantity: 4
         *   --------------
         *   Total: 60
         *
         * Product 2 With Category 1 ($this->product2WithCategory1)
         *   - Price: 21
         *   - Quantity: 5
         *   --------------
         *   Total: 105
         *
         * Product With Category 2 ($this->productWithCategory2)
         *   - Price: 25
         *   - Quantity: 7
         *   --------------
         *   Total: 175
         *
         *  Total Cart Price Without Any Discount: 30 + 18 + 60 + 105 + 175 = 388
         *
         * ----------------- Discounts Individual Breakdown ------------------
         * Discounts
         *   - 5% off ($this->discountRule5PercentOffProductDirectly)
         *     - Applies to
         *       - Product Without Any Category ($this->productWithoutCategory)
         *         - In Cart
         *           - 30 (no product variant) -> (qt:3) 28.5 (1.5 off)
         *           - 18 (product variant selected) -> (qt:2) 17.1 (0.9 off)
         *
         *   - 3% off ($this->discountRule3PercentOffCategory1)
         *     - Applies to
         *       - Product 1 With Category 1 ($this->product1WithCategory1)
         *         - In Cart
         *           - 60 -> (qt:4) 58.2 (0.8 off)
         *       - Product 2 With Category 1 ($this->product1WithCategory1)
         *         - In Cart
         *           - 105 -> (qt:5) 101.85 (3.15 off)
         *
         *   - 1.2 off ($this->discountRule1point2OffCategory2)
         *     - Applies to
         *       - Product With Category 2 ($this->productWithCategory2)
         *         - In Cart
         *           - 175 -> (qt:7) 166.6 (8.4 off)
         *
         *   - 3.3% off ($this-discountRule3point3PercentOffProduct1Category1)
         *     - Applies to
         *       - Product 1 With Category 1 ($this->product1WithCategory1)
         *         - In Cart
         *           - 60 -> (qt:4) 58.02 (1.98 off)
         */

        $this->cart->refresh();
        $this->productWithoutCategory->refresh();
        $this->product1WithCategory1->refresh();
        $this->product2WithCategory1->refresh();
        $this->product2WithCategory1->refresh();
        $this->discountRule1point2OffCategory2->refresh();
        $this->discountRule3point3PercentOffProduct1Category1->refresh();
        $this->discountRule3PercentOffCategory1->refresh();
        $this->discountRule5PercentOffProductDirectly->refresh();
        $this->voucherCode2point5Off->refresh();
        $this->voucherCode10PercentOff->refresh();
    }

    /**
     * @test
     */
    public function test_discount_stacking_on_without_voucher_code(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', true);

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(370.29, round($res->json('totals.total_price'), 2));
        $this->assertEquals(17.71, round($res->json('totals.total_discount'), 2));

        $res = $this->signIn($this->user)->sendCreatePendingOrderRequest([
            "cart_id" => $this->cart->id,
            "address_id" => $this->address->id,
            "delivery_type_id" => $this->deliveryType->id,
            "guest_checkout" => false,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $res->json('data.0.order_id'),
            'status' => OrderStatus::AwaitingPayment,
            'original_price' => 388,
            'total_price' => 375.29,
            'delivery_cost' => 5,
            'total_discount' => 17.71
        ]);
    }

    /**
     * @test
     */
    public function test_discount_stacking_on_with_voucher_code_on_final_price(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', true);
        Config::set('shoptopus.discount_rules.voucher_code_basis', "final_price");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(370.29, round($res->json('totals.total_price'), 2));
        $this->assertEquals(17.71, round($res->json('totals.total_discount'), 2));

        $res = $this->sendApplyVoucherCodeRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode2point5Off->code
        ]);

        $this->assertEquals(388, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(367.79, round($res->json('data.voucher_code_details.cart_totals.total_price'), 2));
        $this->assertEquals(20.21, round($res->json('data.voucher_code_details.cart_totals.total_discount'), 2));

        $res = $this->signIn($this->user)->sendCreatePendingOrderRequest([
            "cart_id" => $this->cart->id,
            "address_id" => $this->address->id,
            "delivery_type_id" => $this->deliveryType->id,
            "guest_checkout" => false,
            "voucher_code" => $this->voucherCode2point5Off->code
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $res->json('data.0.order_id'),
            'status' => OrderStatus::AwaitingPayment,
            'original_price' => 388,
            'total_price' => 372.79,
            'delivery_cost' => 5,
            'total_discount' => 20.21,
            'voucher_code_id' => $this->voucherCode2point5Off->id
        ]);

    }

    /**
     * @test
     */
    public function test_discount_stacking_on_with_voucher_code_on_total_price(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', true);
        Config::set('shoptopus.discount_rules.voucher_code_basis', "total_price");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(370.29, round($res->json('totals.total_price'), 2));
        $this->assertEquals(17.71, round($res->json('totals.total_discount'), 2));

        $res = $this->sendApplyVoucherCodeRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode2point5Off->code
        ]);

        $this->assertEquals(388, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(385.5, round($res->json('data.voucher_code_details.cart_totals.total_price'), 2));
        $this->assertEquals(2.5, round($res->json('data.voucher_code_details.cart_totals.total_discount'), 2));

        $res = $this->signIn($this->user)->sendCreatePendingOrderRequest([
            "cart_id" => $this->cart->id,
            "address_id" => $this->address->id,
            "delivery_type_id" => $this->deliveryType->id,
            "guest_checkout" => false,
            "voucher_code" => $this->voucherCode2point5Off->code
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $res->json('data.0.order_id'),
            'status' => OrderStatus::AwaitingPayment,
            'original_price' => 388,
            'total_price' => 390.5,
            'delivery_cost' => 5,
            'total_discount' => 2.5,
            'voucher_code_id' => $this->voucherCode2point5Off->id
        ]);

    }

    /**
     * @test
     */
    public function test_discount_stacking_off_multiple_discounts_without_voucher_code_discount_highest(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', false);
        Config::set('shoptopus.discount_rules.applied_discount', "highest");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(372.09, round($res->json('totals.total_price'), 2));
        $this->assertEquals(15.91, round($res->json('totals.total_discount'), 2));
    }

    /**
     * @test
     */
    public function test_discount_stacking_off_multiple_discounts_without_voucher_code_discount_lowest(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', false);
        Config::set('shoptopus.discount_rules.applied_discount', "lowest");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(372.25, round($res->json('totals.total_price'), 2));
        $this->assertEquals(15.75, round($res->json('totals.total_discount'), 2));
    }

    /**
     * @test
     */
    public function test_discount_stacking_off_multiple_discounts_with_voucher_code_discount_highest_voucher_code_on_total_price(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', false);
        Config::set('shoptopus.discount_rules.applied_discount', "highest");
        Config::set('shoptopus.discount_rules.voucher_code_basis', "total_price");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(372.09, round($res->json('totals.total_price'), 2));
        $this->assertEquals(15.91, round($res->json('totals.total_discount'), 2));

        $res = $this->sendApplyVoucherCodeRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode2point5Off->code
        ]);

        $this->assertEquals(388, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(385.5, round($res->json('data.voucher_code_details.cart_totals.total_price'), 2));
        $this->assertEquals(2.5, round($res->json('data.voucher_code_details.cart_totals.total_discount'), 2));
    }

    /**
     * @test
     */
    public function test_discount_stacking_off_multiple_discounts_with_voucher_code_discount_lowest_voucher_code_on_total_price(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', false);
        Config::set('shoptopus.discount_rules.applied_discount', "lowest");
        Config::set('shoptopus.discount_rules.voucher_code_basis', "total_price");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(372.25, round($res->json('totals.total_price'), 2));
        $this->assertEquals(15.75, round($res->json('totals.total_discount'), 2));

        $res = $this->sendApplyVoucherCodeRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode2point5Off->code
        ]);

        $this->assertEquals(388, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(385.5, round($res->json('data.voucher_code_details.cart_totals.total_price'), 2));
        $this->assertEquals(2.5, round($res->json('data.voucher_code_details.cart_totals.total_discount'), 2));
    }

    /**
     * @test
     */
    public function test_discount_stacking_off_multiple_discounts_with_voucher_code_discount_highest_voucher_code_on_final_price(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', false);
        Config::set('shoptopus.discount_rules.applied_discount', "highest");
        Config::set('shoptopus.discount_rules.voucher_code_basis', "final_price");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(372.09, round($res->json('totals.total_price'), 2));
        $this->assertEquals(15.91, round($res->json('totals.total_discount'), 2));

        $res = $this->sendApplyVoucherCodeRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode2point5Off->code
        ]);

        $this->assertEquals(388, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(369.59, round($res->json('data.voucher_code_details.cart_totals.total_price'), 2));
        $this->assertEquals(18.41, round($res->json('data.voucher_code_details.cart_totals.total_discount'), 2));
    }

    /**
     * @test
     */
    public function test_discount_stacking_off_multiple_discounts_with_voucher_code_discount_lowest_voucher_code_on_final_price(): void
    {
        Config::set('shoptopus.discount_rules.allow_discount_stacking', false);
        Config::set('shoptopus.discount_rules.applied_discount', "lowest");
        Config::set('shoptopus.discount_rules.voucher_code_basis', "final_price");

        $res = $this->sendShowCartRequest([
            'cart' => $this->cart->id
        ]);

        $this->assertEquals(388, $res->json('totals.original_price'));
        $this->assertEquals(372.25, round($res->json('totals.total_price'), 2));
        $this->assertEquals(15.75, round($res->json('totals.total_discount'), 2));

        $res = $this->sendApplyVoucherCodeRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode2point5Off->code
        ]);

        $this->assertEquals(388, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(369.75, round($res->json('data.voucher_code_details.cart_totals.total_price'), 2));
        $this->assertEquals(18.25, round($res->json('data.voucher_code_details.cart_totals.total_discount'), 2));
    }

    protected function sendShowCartRequest($data = []): TestResponse
    {
        return $this->getJson(route('api.cart.show', $data));
    }

    protected function sendApplyVoucherCodeRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.apply-voucher-code', $data));
    }

    protected function sendCreatePendingOrderRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.create.pending-order', $data));
    }

}
