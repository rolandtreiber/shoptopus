<?php

namespace Tests\PublicApi\Payments;

use Database\Seeders\PaymentProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PaymentTestCase;

class GetClientSettingsTest extends PaymentTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentProviderSeeder::class);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_has_all_required_fields(): void
    {
        $data = [
            'provider' => null,
            'orderId' => null,
        ];

        $this->expectException(\Illuminate\Routing\Exceptions\UrlGenerationException::class);
        $this->sendRequest($data);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_requires_an_existing_order_id(): void
    {
        $data = [
            'provider' => 'stripe',
            'orderId' => 'random-order-id',
        ];

        $this->sendRequest($data)->assertJsonValidationErrors(['orderId']);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.payment.get.settings.public', $data));
    }
}
