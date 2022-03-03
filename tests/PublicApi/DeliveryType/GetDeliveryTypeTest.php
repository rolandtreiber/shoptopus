<?php

namespace Tests\PublicApi\DeliveryType;

use Tests\TestCase;
use App\Models\Order;
use App\Models\DeliveryType;
use App\Models\DeliveryRule;
use App\Services\Local\Order\OrderService;
use App\Services\Local\Error\ErrorService;
use App\Repositories\Local\Order\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\DeliveryType\DeliveryTypeRepository;

class GetDeliveryTypeTest extends TestCase
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
//     * @group apiGet
//     */
//    public function unauthenticated_users_are_not_allowed_to_get_delivery_types()
//    {
//        $unAuthenticatedRes = $this->sendRequest()->json();
//
//        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);
//    }

//    /**
//     * @test
//     * @group apiGet
//     */
//    public function unauthorized_users_are_not_allowed_to_get_delivery_types()
//    {
//        $res = $this->sendRequest()->json();
//
//        $this->assertEquals('This action is unauthorized.', $res['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
//    }

    /**
     * @test
     * @group apiGet
     */
    public function it_can_return_a_delivery_type_by_its_id()
    {
        $this->sendRequest()->assertOk();
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_all_required_fields()
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    $this->getModelRepo()->getSelectableColumns(false)
                ]
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_delivery_rules()
    {
        DeliveryRule::factory()->count(2)->create([
            'delivery_type_id' => $this->delivery_type->id
        ]);

        DeliveryRule::factory()->create([
            'delivery_type_id' => $this->delivery_type->id,
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
     * @group apiGet
     */
    public function it_returns_the_associated_orders()
    {
        Order::factory()->count(2)->create([
            'delivery_type_id' => $this->delivery_type->id
        ]);

        Order::factory()->create([
            'delivery_type_id' => $this->delivery_type->id,
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

    protected function getModelRepo() : DeliveryTypeRepository
    {
        $errorService = new ErrorService;
        $orderService = new OrderService($errorService, new OrderRepository($errorService, new Order));
        return new DeliveryTypeRepository($errorService, new DeliveryType, $orderService);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.delivery_types.get', ['id' => $this->delivery_type->id]));
    }
}
