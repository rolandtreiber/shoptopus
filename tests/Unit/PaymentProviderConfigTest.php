<?php

namespace Tests\Unit;

use App\Models\PaymentProvider\PaymentProvider;
use App\Models\PaymentProvider\PaymentProviderConfig;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentProviderConfigTest extends TestCase
{
    use RefreshDatabase;

    protected $payment_provider_config;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);

        $this->payment_provider_config = PaymentProviderConfig::first();
    }

    /** @test */
    public function it_has_a_setting_field()
    {
        $this->assertNotNull($this->payment_provider_config->setting);
    }

    /** @test */
    public function it_has_a_value_field()
    {
        $this->assertNotNull($this->payment_provider_config->value);
    }

    /** @test */
    public function it_has_a_test_value_field()
    {
        $this->assertNotNull($this->payment_provider_config->test_value);
    }

    /** @test */
    public function it_belongs_to_payment_provider()
    {
        $this->assertInstanceOf(PaymentProvider::class, $this->payment_provider_config->payment_provider);
    }
}
