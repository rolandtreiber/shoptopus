<?php

namespace Tests\Unit;

use App\Models\PaymentProvider\PaymentProvider;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentProviderTest extends TestCase
{
    use RefreshDatabase;

    protected $payment_provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);

        $this->payment_provider = PaymentProvider::first();
    }

    /** @test */
    public function it_has_a_name_field()
    {
        $this->assertNotNull($this->payment_provider->name);
    }

    /** @test */
    public function it_has_an_enabled_field()
    {
        $this->assertIsBool($this->payment_provider->enabled);
    }

    /** @test */
    public function it_has_a_test_mode_field()
    {
        $this->assertIsBool($this->payment_provider->test_mode);
    }

    /** @test */
    public function it_may_have_many_payment_provider_configs()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->payment_provider->payment_provider_configs);
    }
}
