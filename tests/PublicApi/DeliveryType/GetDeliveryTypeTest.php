<?php

namespace Tests\PublicApi\DeliveryType;

use Tests\TestCase;
use App\Models\Order;
use App\Models\DeliveryType;
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
//        $res = $this->signIn()->sendRequest()->json();
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
        $this->signIn()
            ->sendRequest()
            ->assertOk()
            ->assertSee($this->delivery_type->code);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_all_required_fields()
    {
        $this->signIn()
            ->sendRequest()
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
    public function it_returns_the_corresponding_orders()
    {
        Order::factory()->count(2)->create([
            'delivery_type_id' => $this->delivery_type->id
        ]);

        $res = $this->signIn()->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                array_merge($this->getModelRepo()->getSelectableColumns(false))
            ]
        ]);

        $this->assertCount(2, $res->json('data.0.orders'));
    }

    protected function getModelRepo() : DeliveryTypeRepository
    {
        $errorService = new ErrorService;
        return new DeliveryTypeRepository($errorService, new DeliveryType);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.delivery_types.get', ['id' => $this->delivery_type->id]));
    }
}
