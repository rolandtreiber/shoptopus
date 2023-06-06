<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\PaymentSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentSourceTest extends TestCase
{
    use RefreshDatabase;

    protected $payment_source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment_source = PaymentSource::factory()->create();
    }

    /** @test */
    public function it_has_a_generated_slug(): void
    {
        $string = $this->payment_source->user->last_name.' '.$this->payment_source->name;
        $this->assertEquals(Str::slug($string), $this->payment_source->slug);
    }

    /** @test */
    public function it_has_a_name_field(): void
    {
        $this->assertNotNull($this->payment_source->name);
    }
//
//    /** @test */
//    public function it_has_a_source_id_field()
//    {
//        $this->assertNull($this->payment_source->source_id);
//    }

//    /** @test */
//    public function it_has_an_exp_month_field()
//    {
//        $this->assertNull($this->payment_source->exp_month);
//    }
//
//    /** @test */
//    public function it_has_an_exp_year_field()
//    {
//        $this->assertNull($this->payment_source->exp_year);
//    }
//
//    /** @test */
//    public function it_has_a_last_four_field()
//    {
//        $this->assertNull($this->payment_source->last_four);
//    }
//
//    /** @test */
//    public function it_has_a_brand_field()
//    {
//        $this->assertNull($this->payment_source->brand);
//    }
//
//    /** @test */
//    public function it_has_a_stripe_user_id_field()
//    {
//        $this->assertNull($this->payment_source->stripe_user_id);
//    }
//
//    /** @test */
//    public function it_has_a_payment_method_id_field()
//    {
//        $this->assertNull($this->payment_source->payment_method_id);
//    }

    /** @test */
    public function it_may_belong_to_a_user(): void
    {
        //$this->assertNull($this->payment_source->user);

        //$this->payment_source->update(['user_id' => User::factory()->create()->id]);

        $this->assertInstanceOf(User::class, $this->payment_source->user);
    }

    /** @test */
    public function it_may_have_many_payments(): void
    {
        $this->assertEmpty($this->payment_source->payments);

        $this->payment_source->payments()->save(Payment::factory()->create());

        $this->assertInstanceOf(Payment::class, $this->payment_source->fresh()->payments[0]);
    }
}
