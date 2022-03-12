<?php

namespace Tests\PublicApi\DeliveryRule;

use Tests\TestCase;
use App\Models\DeliveryRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDeliveryRuleTest extends TestCase
{
    use RefreshDatabase;

    protected $delivery_rule;

    public function setUp() : void
    {
        parent::setUp();

        $this->delivery_rule = DeliveryRule::factory()->create();
    }

//    /**
//     * @test
//     * @group apiPatch
//     */
//    public function unauthorised_users_are_not_allowed_to_update_delivery_rules()
//    {
//        $data = DeliveryRule::factory()->raw();
//
//        $unAuthenticatedRes = $this->sendRequest($data)->json();
//
//        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);
//
//        $unAuthorisedRes = $this->signIn()->sendRequest($data)->json();
//
//        $this->assertEquals('This action is unauthorized.', $unAuthorisedRes['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthorisedRes['user_message']);
//    }

    /**
     * @test
     * @group apiPatch
     */
    public function authorised_users_can_update_delivery_rules()
    {
        $data = DeliveryRule::factory()->raw();

        $this->sendRequest($data)->assertOk();

        $data['postcodes'] = json_encode($data['postcodes']);

        $this->assertDatabaseHas('delivery_rules', $data);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('api.delivery_rules.update', ['id' => $this->delivery_rule->id]), $data);
    }
}
