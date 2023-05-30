<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = Payment::factory()->create();
    }

//    /** @test */
//    public function it_has_a_generated_slug()
//    {
//        $this->assertEquals(Str::slug($this->payment->payable->slug), $this->payment->slug);
//    }

    /** @test */
    public function it_has_a_payable_type_field(): void
    {
        $this->assertEquals(basename(Order::class), $this->payment->payable_type);
    }

    /** @test */
    public function it_has_a_payable_id_field(): void
    {
        $this->assertNotNull($this->payment->payable_id);
    }

    /** @test */
    public function it_has_an_amount_field(): void
    {
        $this->assertNotNull($this->payment->amount);
    }

    /** @test */
    public function it_has_a_proof_field(): void
    {
        $this->assertNull($this->payment->proof);
    }

    /** @test */
    public function it_may_belong_to_a_user(): void
    {
        $this->assertNull($this->payment->user);

        $this->payment->update(['user_id' => User::factory()->create()->id]);

        $this->assertInstanceOf(User::class, $this->payment->fresh()->user);
    }

    /** @test */
    public function it_may_belong_to_a_payment_source(): void
    {
        $this->assertNull($this->payment->payment_source);

        $this->payment->update(['payment_source_id' => PaymentSource::factory()->create()->id]);

        $this->assertInstanceOf(PaymentSource::class, $this->payment->fresh()->payment_source);
    }

    /** @test */
    public function it_may_belongs_to_a_payable(): void
    {
        $this->assertInstanceOf(Order::class, $this->payment->payable);
    }
}
