<?php

namespace Tests\Unit;

use App\Models\PaymentSource;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $payment;

    public function setUp() : void
    {
        parent::setUp();

        $this->payment = Payment::factory()->create();
    }

    /** @test */
    public function it_has_a_generated_slug()
    {
        $this->assertEquals(Str::slug($this->payment->payable->slug), $this->payment->slug);
    }

    /** @test */
    public function it_has_a_payable_type_field()
    {
        $this->assertEquals(basename(Order::class), $this->payment->payable_type);
    }

    /** @test */
    public function it_has_a_payable_id_field()
    {
        $this->assertNotNull($this->payment->payable_id);
    }

    /** @test */
    public function it_has_an_amount_field()
    {
        $this->assertNotNull($this->payment->amount);
    }

    /** @test */
    public function it_has_a_proof_field()
    {
        $this->assertNull($this->payment->proof);
    }

    /** @test */
    public function it_may_belong_to_a_user()
    {
        $this->assertNull($this->payment->user);

        $this->payment->update(['user_id' => User::factory()->create()->id]);

        $this->assertInstanceOf(User::class, $this->payment->fresh()->user);
    }

    /** @test */
    public function it_may_belong_to_a_payment_source()
    {
        $this->assertNull($this->payment->payment_source);

        $this->payment->update(['payment_source_id' => PaymentSource::factory()->create()->id]);

        $this->assertInstanceOf(PaymentSource::class, $this->payment->fresh()->payment_source);
    }

    /** @test */
    public function it_may_belongs_to_a_payable()
    {
        $this->assertInstanceOf(Order::class, $this->payment->payable);
    }
}
