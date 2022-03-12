<?php

namespace Tests\PublicApi\DeliveryRule;

use Tests\TestCase;
use App\Models\DeliveryRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteDeliveryRuleTest extends TestCase
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
//     * @group apiDelete
//     */
//    public function unauthorised_users_are_not_allowed_to_delete_delivery_rules()
//    {
//        $unAuthenticatedRes = $this->sendRequest()->json();
//
//        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);
//
//        $unAuthorisedRes = $this->signIn()->sendRequest()->json();
//
//        $this->assertEquals('This action is unauthorized.', $unAuthorisedRes['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthorisedRes['user_message']);
//    }

    /**
     * @test
     * @group apiDelete
     */
    public function authorised_users_can_delete_delivery_rules()
    {
//        $user = User::factory()->create();

        $this->assertDatabaseHas('delivery_rules', [
            'id' => $this->delivery_rule->id,
            'deleted_at' => null
        ]);

        $this->sendRequest()->assertOk();

//        $this->signIn($user)->sendRequest()->assertOk();

        $this->assertDatabaseHas('delivery_rules', [
            'id' => $this->delivery_rule->id,
            'deleted_at' => now()
        ]);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('api.delivery_rules.delete', ['id' => $this->delivery_rule->id]), $data);
    }
}
