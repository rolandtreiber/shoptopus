<?php

namespace Tests\PublicApi\VoucherCode;

use Tests\TestCase;
use App\Models\Order;
use App\Models\VoucherCode;
use App\Services\Local\Order\OrderService;
use App\Services\Local\Error\ErrorService;
use App\Repositories\Local\Order\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\VoucherCode\VoucherCodeRepository;

class GetVoucherCodeTest extends TestCase
{
    use RefreshDatabase;

    protected $voucher_code;

    public function setUp() : void
    {
        parent::setUp();

        $this->voucher_code = VoucherCode::factory()->create();
    }

//    /**
//     * @test
//     * @group apiGet
//     */
//    public function unauthenticated_users_are_not_allowed_to_get_voucher_codes()
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
//    public function unauthorized_users_are_not_allowed_to_get_voucher_codes()
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
    public function it_can_return_a_voucher_code_by_its_id()
    {
        $this->signIn()
            ->sendRequest()
            ->assertOk()
            ->assertSee($this->voucher_code->code);
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
            'voucher_code_id' => $this->voucher_code->id
        ]);

        $res = $this->signIn()->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                array_merge($this->getModelRepo()->getSelectableColumns(false), ['orders'])
            ]
        ]);

        $this->assertCount(2, $res->json('data.0.orders'));
    }

    protected function getModelRepo() : VoucherCodeRepository
    {
        $errorService = new ErrorService;
        $orderService = new OrderService($errorService, new OrderRepository($errorService, new Order));
        return new VoucherCodeRepository($errorService, new VoucherCode, $orderService);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.voucher_codes.get', ['id' => $this->voucher_code->id]));
    }
}
