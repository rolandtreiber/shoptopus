<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Order;
use App\Models\VoucherCode;
use App\Enums\DiscountType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherCodeTest extends TestCase
{
    use RefreshDatabase;

    protected $voucher_code;

    public function setUp() : void
    {
        parent::setUp();

        $this->voucher_code = VoucherCode::factory()->create();
    }

    /** @test */
    public function it_has_a_type_field()
    {
        $this->assertNotNull($this->voucher_code->type);
    }

    /** @test */
    public function it_has_an_amount_field()
    {
        $this->assertNotNull($this->voucher_code->amount);
    }

    /** @test */
    public function it_has_a_code()
    {
        $this->assertNotNull($this->voucher_code->code);
    }

    /** @test */
    public function it_has_a_valid_from_field()
    {
        $this->assertNotNull($this->voucher_code->valid_from);

        $this->assertInstanceOf(Carbon::class, $this->voucher_code->valid_from);
    }

    /** @test */
    public function it_has_a_valid_until_field()
    {
        $this->assertNotNull($this->voucher_code->valid_until);

        $this->assertInstanceOf(Carbon::class, $this->voucher_code->valid_until);
    }

    /** @test */
    public function it_has_an_enabled_field()
    {
        $this->assertTrue($this->voucher_code->enabled);
    }

    /** @test */
    public function it_can_return_a_readable_value()
    {
        $this->voucher_code->update(['amount' => '15.00']);

        $this->assertEquals('15%', $this->voucher_code->value);

        $this->voucher_code->update(['type' => DiscountType::Amount]);

        $this->assertEquals('£15', $this->voucher_code->fresh()->value);
    }

    /** @test */
    public function it_may_have_many_orders()
    {
        $this->assertEmpty($this->voucher_code->orders);

        Order::factory()->create(['voucher_code_id' => $this->voucher_code->id]);

        $this->voucher_code->refresh();

        $this->assertCount(1, $this->voucher_code->orders);

        $this->assertInstanceOf(Order::class, $this->voucher_code->orders[0]);
    }
}
