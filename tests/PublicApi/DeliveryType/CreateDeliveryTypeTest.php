<?php

namespace Tests\PublicApi\DeliveryType;

use Tests\TestCase;
use App\Models\DeliveryType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateDeliveryTypeTest extends TestCase
{
    use RefreshDatabase;

//    /**
//     * @test
//     * @group apiPost
//     */
//    public function unauthorised_users_are_not_allowed_to_create_delivery_types()
//    {
//        $data = DeliveryType::factory()->raw();
//
//        $res = $this->sendRequest($data)->json();
//
//        $this->assertEquals('Unauthenticated.', $res['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
//    }

    /**
     * @test
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        $data = [
            'name' => [
                'en' => null
            ],
            'description' => [
                'en' => null
            ]
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['name.en', 'description.en']);

        $this->assertDatabaseMissing('delivery_types', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function authorised_users_can_create_delivery_types()
    {
        $data = DeliveryType::factory()->raw();

        $this->sendRequest($data)->assertOk();

        $this->assertDatabaseHas('delivery_types', [
            'name' => json_encode($data['name']),
            'description' => json_encode($data['description']),
            'price' => $data['price']
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_price_must_match_the_exact_number_of_characters()
    {
        $data = DeliveryType::factory()->raw(['price' => 20]);

        $this->sendRequest($data)->assertJsonValidationErrors(['price']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.delivery_types.create'), $data);
    }
}
