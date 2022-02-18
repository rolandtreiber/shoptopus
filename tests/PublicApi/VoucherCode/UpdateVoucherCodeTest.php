<?php

namespace Tests\PublicApi\VoucherCode;

use Tests\TestCase;
use App\Models\VoucherCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateVoucherCodeTest extends TestCase
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
//     * @group apiPatch
//     */
//    public function unauthorised_users_are_not_allowed_to_update_voucher_codes()
//    {
//        $data = VoucherCode::factory()->raw();
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
    public function authorised_users_can_update_voucher_codes()
    {
//        $user = User::factory()->create();

        $data = VoucherCode::factory()->raw();

        $this->signIn()->sendRequest($data)->assertOk();

//        $this->signIn($user)->sendRequest($data)->assertOk();

        $this->assertDatabaseHas('voucher_codes', $data);
    }

    /**
     * @test
     * @group apiPatch
     */
    public function the_amount_must_match_the_exact_number_of_characters()
    {
        $data = VoucherCode::factory()->raw(['amount' => 20]);

        $this->signIn()->sendRequest($data)->assertJsonValidationErrors(['amount']);
    }

    /**
     * @test
     * @group apiPatch
     */
    public function the_type_must_be_one_of_the_predefined_discount_types()
    {
        $data = VoucherCode::factory()->raw(['type' => 4]);

        $this->signIn()->sendRequest($data)->assertJsonValidationErrors(['type']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('api.voucher_codes.update', ['id' => $this->voucher_code->id]), $data);
    }
}
