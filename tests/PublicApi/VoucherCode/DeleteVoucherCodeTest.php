<?php

namespace Tests\PublicApi\VoucherCode;

use Tests\TestCase;
use App\Models\VoucherCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteVoucherCodeTest extends TestCase
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
//     * @group apiDelete
//     */
//    public function unauthorised_users_are_not_allowed_to_delete_voucher_codes()
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
    public function authorised_users_can_delete_voucher_codes()
    {
//        $user = User::factory()->create();

        $this->assertDatabaseHas('voucher_codes', [
            'id' => $this->voucher_code->id,
            'deleted_at' => null
        ]);

        $this->signIn()->sendRequest()->assertOk();

//        $this->signIn($user)->sendRequest()->assertOk();

        $this->assertDatabaseHas('voucher_codes', [
            'id' => $this->voucher_code->id,
            'deleted_at' => now()
        ]);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('api.voucher_codes.delete', ['id' => $this->voucher_code->id]), $data);
    }
}
