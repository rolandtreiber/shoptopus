<?php

namespace Tests\PublicApi\DeliveryType;

use Tests\TestCase;
use App\Models\Order;
use App\Models\DeliveryType;
use App\Models\DeliveryRule;
use App\Services\Local\Error\ErrorService;
use App\Services\Local\Order\OrderService;
use App\Repositories\Local\Order\OrderRepository;
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
    public function it_returns_the_associated_delivery_rules()
    {
        $dt = DeliveryType::factory()->has(DeliveryRule::factory()->count(2), 'deliveryRules')->create();

        DeliveryRule::factory()->create([
            'delivery_type_id' => $dt->first()->id,
            'deleted_at' => now()
        ]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'delivery_rules' => [
                        [
                            'id',
                            'postcodes',
                            'min_weight',
                            'max_weight',
                            'min_distance',
                            'max_distance',
                            'distance_unit',
                            'lat',
                            'lon',
                            'enabled'
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertCount(2, $res->json('data.0.delivery_rules'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_orders()
    {
        $dt =  DeliveryType::factory()->has(Order::factory()->count(2), 'orders')->create();

        Order::factory()->create([
            'delivery_type_id' => $dt->first()->id,
            'deleted_at' => now()
        ]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'orders' => [
                        [
                            'id',
                            'user_id',
                            'voucher_code_id',
                            'address_id',
                            'original_price',
                            'subtotal',
                            'total_price',
                            'total_discount',
                            'delivery_cost',
                            'status'
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertCount(2, $res->json('data.0.orders'));
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

        $res = $this->sendRequest();

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
        $orderService = new OrderService($errorService, new OrderRepository($errorService, new Order));
        return new DeliveryTypeRepository($errorService, new DeliveryType, $orderService);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.delivery_types.getAll', $data));
    }
}
