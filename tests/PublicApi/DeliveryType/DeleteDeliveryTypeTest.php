<?php

namespace Tests\PublicApi\DeliveryType;

use Tests\TestCase;
use App\Models\DeliveryType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteDeliveryTypeTest extends TestCase
{
    use RefreshDatabase;

    protected $delivery_type;

    public function setUp() : void
    {
        parent::setUp();

        $this->delivery_type = DeliveryType::factory()->create();
    }

//    /**
//     * @test
//     * @group apiDelete
//     */
//    public function unauthorised_users_are_not_allowed_to_delete_delivery_types()
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
    public function authorised_users_can_delete_delivery_types()
    {
//        $user = User::factory()->create();

        $this->assertDatabaseHas('delivery_types', [
            'id' => $this->delivery_type->id,
            'deleted_at' => null
        ]);

        $this->sendRequest()->assertOk();

//        $this->signIn($user)->sendRequest()->assertOk();

        $this->assertDatabaseHas('delivery_types', [
            'id' => $this->delivery_type->id,
            'deleted_at' => now()
        ]);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('api.delivery_types.delete', ['id' => $this->delivery_type->id]), $data);
    }
}
