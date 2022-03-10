<?php

namespace Tests\PublicApi\VoucherCode;

use Tests\TestCase;
use App\Models\Order;
use App\Models\VoucherCode;
use App\Services\Local\Error\ErrorService;
use App\Services\Local\Order\OrderService;
use App\Repositories\Local\Order\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\VoucherCode\VoucherCodeRepository;

class GetAllVoucherCodesTest extends TestCase
{
    use RefreshDatabase;

//    /**
//     * @test
//     * @group apiGetAll
//     */
//    public function unauthorized_users_are_not_allowed_to_get_all_their_voucher_codes()
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
        VoucherCode::factory()->count(2)->create();

        $res = $this->signIn()->sendRequest();

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
    public function soft_deleted_voucher_codes_are_not_returned()
    {
        VoucherCode::factory()->count(2)->create([
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
        VoucherCode::factory()->count(2)->create();

        $this->assertEquals(2, $this->signIn()->sendRequest()->json('total_records'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function voucher_codes_can_be_filtered_by_id()
    {
        VoucherCode::factory()->count(3)->create();
        $voucher_code = VoucherCode::factory()->create();

        $res = $this->signIn()->sendRequest(['filter[id]' => $voucher_code->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($voucher_code->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters()
    {
        VoucherCode::factory()->count(3)->create();
        $voucher_code1 = VoucherCode::factory()->create();
        $voucher_code2 = VoucherCode::factory()->create();

        $res = $this->signIn()->sendRequest(['filter[id]' => implode(',', [$voucher_code1->id, $voucher_code2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($voucher_code1->id, $res->json('data.0.id'));
        $this->assertEquals($voucher_code2->id, $res->json('data.1.id'));
    }

    protected function getModelRepo() : VoucherCodeRepository
    {
        $errorService = new ErrorService;
        $orderService = new OrderService($errorService, new OrderRepository($errorService, new Order));
        return new VoucherCodeRepository($errorService, new VoucherCode, $orderService);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.voucher_codes.getAll', $data));
    }
}
