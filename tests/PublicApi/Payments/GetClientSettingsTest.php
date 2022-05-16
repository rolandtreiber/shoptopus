<?php

namespace Tests\PublicApi\Payments;

use Tests\TestCase;
use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetClientSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_has_all_required_fields()
    {
        $data = [
            'provider' => null,
            'orderId' => null
        ];

        $this->expectException(\Illuminate\Routing\Exceptions\UrlGenerationException::class);
        $this->sendRequest($data);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_requires_an_existing_order_id()
    {
        $data = [
            'provider' => 'stripe',
            'orderId' => "random-order-id"
        ];

        $this->sendRequest($data)->assertJsonValidationErrors(['orderId']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.payment.get.settings.public', $data));
    }
}
