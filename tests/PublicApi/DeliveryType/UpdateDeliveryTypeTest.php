<?php

namespace Tests\PublicApi\DeliveryType;

use Tests\TestCase;
use App\Models\DeliveryType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDeliveryTypeTest extends TestCase
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
//     * @group apiPatch
//     */
//    public function unauthorised_users_are_not_allowed_to_update_delivery_types()
//    {
//        $data = DeliveryType::factory()->raw();
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
    public function authorised_users_can_update_delivery_types()
    {
//        $user = User::factory()->create();

        $data = DeliveryType::factory()->raw();

        $this->sendRequest($data)->assertOk();

//        $this->signIn($user)->sendRequest($data)->assertOk();

        $this->assertDatabaseHas('delivery_types', [
            'name' => json_encode($data['name']),
            'description' => json_encode($data['description']),
            'price' => $data['price']
        ]);
    }

    /**
     * @test
     * @group apiPatch
     */
    public function the_price_must_match_the_exact_number_of_characters()
    {
        $data = DeliveryType::factory()->raw(['price' => 20]);

        $this->sendRequest($data)->assertJsonValidationErrors(['price']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('api.delivery_types.update', ['id' => $this->delivery_type->id]), $data);
    }
}
