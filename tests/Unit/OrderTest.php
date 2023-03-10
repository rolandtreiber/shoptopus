<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\VoucherCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = Order::factory()->create();

        $this->order->refresh();
    }

    /** @test */
    public function it_has_an_original_price_field()
    {
        $this->assertIsFloat($this->order->original_price);
    }

    /** @test */
    public function it_has_a_subtotal_field()
    {
        $this->assertIsFloat($this->order->subtotal);
    }

    /** @test */
    public function it_has_a_total_price_field()
    {
        $this->assertIsFloat($this->order->total_price);
    }

    /** @test */
    public function it_has_a_total_discount_field()
    {
        $this->assertIsFloat($this->order->total_discount);
    }

    /** @test */
    public function it_has_a_delivery_cost_field()
    {
        $this->assertIsFloat($this->order->delivery_cost);
    }

    /** @test */
    public function it_has_a_status_field()
    {
        $this->assertEquals(OrderStatus::AwaitingPayment, $this->order->status);
    }

    /** @test */
    public function it_has_a_currency_code_field()
    {
        $this->assertNotNull($this->order->currency_code);
        $this->assertEquals('GBP', $this->order->currency_code);
    }

    /** @test */
    public function it_has_an_address()
    {
        $this->assertInstanceOf(Address::class, $this->order->address);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->order->user);
    }

    /** @test */
    public function it_may_belong_to_a_delivery_type()
    {
        $this->assertNull($this->order->delivery_type);

        $dt = DeliveryType::factory()->create();

        $this->order->update(['delivery_type_id' => $dt->id]);

        $this->assertInstanceOf(DeliveryType::class, $this->order->fresh()->delivery_type);
    }

    /** @test */
    public function it_may_belong_to_a_voucher_code()
    {
        $this->assertNull($this->order->voucher_code);

        $vc = VoucherCode::factory()->create();

        $this->order->update(['voucher_code_id' => $vc->id]);

        $this->assertInstanceOf(VoucherCode::class, $this->order->fresh()->voucher_code);
    }

    /** @test */
    public function it_may_have_many_payments()
    {
        $this->assertEmpty($this->order->payments);

        $this->order->payments()->save(Payment::factory()->create());

        $this->assertInstanceOf(Payment::class, $this->order->fresh()->payments[0]);
    }

    /** @test */
    public function it_may_have_many_products()
    {
        $this->assertEmpty($this->order->products);

        $this->order->products()->save(Product::factory()->create());

        $this->assertInstanceOf(Product::class, $this->order->fresh()->products[0]);
    }
}
