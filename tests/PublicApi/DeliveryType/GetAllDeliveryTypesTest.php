<?php

namespace Tests\PublicApi\DeliveryType;

use Tests\TestCase;
use App\Models\DeliveryType;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\DeliveryType\DeliveryTypeRepository;

class GetAllDeliveryTypesTest extends TestCase
{
    use RefreshDatabase;

//    /**
//     * @test
//     * @group apiGetAll
//     */
//    public function unauthorized_users_are_not_allowed_to_get_all_their_delivery_types()
//    {
//        $res = $this->sendRequest()->json();
//
//        $this->assertEquals('Unauthenticated.', $res['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
//    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_correct_format()
    {
        $this->signIn()
            ->sendRequest()
            ->assertJsonStructure([
                'message',
                'data',
                'next',
                'records',
                'total_records'
            ]);
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_all_required_fields()
    {
        DeliveryType::factory()->count(2)->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                $this->getModelRepo()->getSelectableColumns(false)
            ]
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function soft_deleted_delivery_types_are_not_returned()
    {
        DeliveryType::factory()->count(2)->create([
            'deleted_at' => now()
        ]);

        $res = $this->signIn()->sendRequest();

        $this->assertEmpty($res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_count()
    {
        DeliveryType::factory()->count(2)->create();

        $this->assertEquals(2, $this->signIn()->sendRequest()->json('total_records'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function delivery_types_can_be_filtered_by_id()
    {
        DeliveryType::factory()->count(3)->create();
        $delivery_type = DeliveryType::factory()->create();

        $res = $this->signIn()->sendRequest(['filter[id]' => $delivery_type->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($delivery_type->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters()
    {
        DeliveryType::factory()->count(3)->create();
        $delivery_type1 = DeliveryType::factory()->create();
        $delivery_type2 = DeliveryType::factory()->create();

        $res = $this->signIn()->sendRequest(['filter[id]' => implode(',', [$delivery_type1->id, $delivery_type2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($delivery_type1->id, $res->json('data.0.id'));
        $this->assertEquals($delivery_type2->id, $res->json('data.1.id'));
    }

    protected function getModelRepo() : DeliveryTypeRepository
    {
        $errorService = new ErrorService;
        return new DeliveryTypeRepository($errorService, new DeliveryType);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.delivery_types.getAll', $data));
    }
}
