<?php

namespace PublicApi\Checkout;

use App\Enums\DiscountType;
use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group provision-cart-totals-with-voucher-code
 */
class ProvisionCartTotalsWithVoucherCodeAppliedTest extends TestCase
{

    use RefreshDatabase;

    private Cart $cart;
    private VoucherCode $voucherCode;
    private Collection $products;
    private ProductVariant $productVariant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->voucherCode = VoucherCode::factory()->state([
            'enabled' => 1,
            'valid_from' => Carbon::now()->subDay(),
            'valid_until' => Carbon::now()->addDay(),
            'type' => DiscountType::Percentage,
            'amount' => 10
        ])->create();
        $this->products = Product::factory()->count(3)->create();
        $this->products[0]->price = 10; // 20
        $this->products[1]->price = 20; // Variant: 15, product price ignored, therefore: 30
        $this->products[2]->price = 30; // 60
        $this->products[0]->save();
        $this->products[1]->save();
        $this->products[2]->save();
        $this->productVariant = ProductVariant::factory()->state([
            'product_id' => $this->products[1]->id,
            'price' => 15
        ])->create();
        $this->productVariant->save();
        $this->cart = Cart::factory()->create();
        $this->cart->products()->attach($this->products[0]->id, ['quantity' => 2]);
        $this->cart->products()->attach($this->products[1]->id, ['quantity' => 2, 'product_variant_id' => $this->productVariant->id]);
        $this->cart->products()->attach($this->products[2]->id, ['quantity' => 2]);
    }

    /**
     * @test
     */
    public function test_cart_totals_calculated_correctly_for_valid_percentage_type_voucher_code()
    {
        $res = $this->sendRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode->code
        ]);
        $res->assertOk();
        $this->assertEquals("OK", $res->json('data.status'));
        $this->assertEquals("Percentage", $res->json('data.voucher_code_details.type'));
        $this->assertEquals(10, $res->json('data.voucher_code_details.value'));
        $this->assertEquals(110, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(99, $res->json('data.voucher_code_details.cart_totals.total_price'));
        $this->assertEquals(11, $res->json('data.voucher_code_details.cart_totals.total_discount'));
    }

    /**
     * @test
     */
    public function test_cart_totals_calculated_correctly_for_valid_fix_amount_type_voucher_code()
    {
        $this->voucherCode->amount = 2.75;
        $this->voucherCode->type = DiscountType::Amount;
        $this->voucherCode->save();
        $res = $this->sendRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode->code
        ]);
        $res->assertOk();
        $this->assertEquals("OK", $res->json('data.status'));
        $this->assertEquals("Fix Amount", $res->json('data.voucher_code_details.type'));
        $this->assertEquals(2.75, $res->json('data.voucher_code_details.value'));
        $this->assertEquals(110, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(107.25, $res->json('data.voucher_code_details.cart_totals.total_price'));
        $this->assertEquals(2.75, $res->json('data.voucher_code_details.cart_totals.total_discount'));
    }

    /**
     * @test
     */
    public function test_cart_totals_calculated_correctly_for_valid_fix_amount_type_voucher_code_where_cart_has_inactive_product()
    {
        $this->voucherCode->amount = 2.75;
        $this->voucherCode->type = DiscountType::Amount;
        $this->voucherCode->save();
        $this->products[0]->status = ProductStatus::Discontinued;
        $this->products[0]->save();
        $res = $this->sendRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode->code
        ]);
        $res->assertOk();
        $this->assertEquals("OK", $res->json('data.status'));
        $this->assertEquals("Fix Amount", $res->json('data.voucher_code_details.type'));
        $this->assertEquals(2.75, $res->json('data.voucher_code_details.value'));
        $this->assertEquals(90, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(87.25, $res->json('data.voucher_code_details.cart_totals.total_price'));
        $this->assertEquals(2.75, $res->json('data.voucher_code_details.cart_totals.total_discount'));
    }

    /**
     * @test
     */
    public function test_cart_totals_calculated_correctly_for_valid_percentage_type_voucher_code_where_cart_has_inactive_product()
    {
        $this->products[0]->status = ProductStatus::Discontinued;
        $this->products[0]->save();
        $res = $this->sendRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode->code
        ]);
        $res->assertOk();
        $this->assertEquals("OK", $res->json('data.status'));
        $this->assertEquals("Percentage", $res->json('data.voucher_code_details.type'));
        $this->assertEquals(10, $res->json('data.voucher_code_details.value'));
        $this->assertEquals(90, $res->json('data.voucher_code_details.cart_totals.original_price'));
        $this->assertEquals(81, $res->json('data.voucher_code_details.cart_totals.total_price'));
        $this->assertEquals(9, $res->json('data.voucher_code_details.cart_totals.total_discount'));
    }

    /**
     * @test
     */
    public function test_it_fails_when_voucher_code_is_invalid()
    {
        $res = $this->sendRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => "__INVALID_"
        ]);
        $res->assertOk();
        $this->assertEquals("INVALID", $res->json('data.status'));
        $this->assertNull($res->json('data.voucher_code_details'));
    }

    /**
     * @test
     */
    public function test_it_fails_when_voucher_code_is_expired()
    {
        $this->voucherCode->valid_until = Carbon::now()->subDay();
        $this->voucherCode->save();
        $res = $this->sendRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode->code
        ]);
        $res->assertOk();
        $this->assertEquals("INVALID", $res->json('data.status'));
        $this->assertNull($res->json('data.voucher_code_details'));
    }

    /**
     * @test
     */
    public function test_it_fails_when_voucher_code_has_not_started()
    {
        $this->voucherCode->valid_from = Carbon::now()->addDay();
        $this->voucherCode->save();
        $res = $this->sendRequest([
            'cart_id' => $this->cart->id,
            'voucher_code' => $this->voucherCode->code
        ]);
        $res->assertOk();
        $this->assertEquals("INVALID", $res->json('data.status'));
        $this->assertNull($res->json('data.voucher_code_details'));
    }

    protected function sendRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.apply-voucher-code', $data));
    }


}
