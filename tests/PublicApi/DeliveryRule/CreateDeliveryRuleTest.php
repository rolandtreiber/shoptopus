<?php

namespace Tests\PublicApi\DeliveryRule;

use Tests\TestCase;
use App\Models\DeliveryRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateDeliveryRuleTest extends TestCase
{
    use RefreshDatabase;

//    /**
//     * @test
//     * @group apiPost
//     */
//    public function unauthorised_users_are_not_allowed_to_create_delivery_rules()
//    {
//        $data = DeliveryRule::factory()->raw();
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
            'delivery_type_id' => null
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['delivery_type_id']);

        $this->assertDatabaseMissing('delivery_rules', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function authorised_users_can_create_delivery_rules()
    {
        $data = DeliveryRule::factory()->raw();

        $this->sendRequest($data)->assertOk();

        $data['postcodes'] = json_encode($data['postcodes']);

        $this->assertDatabaseHas('delivery_rules', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_distance_unit_must_be_either_mile_or_kilometer()
    {
        $data = DeliveryRule::factory()->raw(['distance_unit' => 'cm']);

        $this->sendRequest($data)->assertJsonValidationErrors(['distance_unit']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.delivery_rules.create'), $data);
    }
}
