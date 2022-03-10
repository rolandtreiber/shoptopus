<?php

namespace Tests\PublicApi\VoucherCode;

use Tests\TestCase;
use App\Models\VoucherCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateVoucherCodeTest extends TestCase
{
    use RefreshDatabase;

//    /**
//     * @test
//     * @group apiPost
//     */
//    public function unauthorised_users_are_not_allowed_to_create_voucher_codes()
//    {
//        $data = VoucherCode::factory()->raw();
//
//        $res = $this->sendRequest($data)->json();
//
//        $this->assertEquals('Unauthenticated.', $res['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
//    }

    /**
     * @test
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        $data = [
            'amount' => null,
            'valid_from' => null,
            'valid_until' => null
        ];

        $this->signIn()
            ->sendRequest($data)
            ->assertJsonValidationErrors(['amount', 'valid_from', 'valid_until']);

        $this->assertDatabaseMissing('voucher_codes', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function authorised_users_can_create_voucher_codes()
    {
        $data = VoucherCode::factory()->raw();

        $this->signIn()->sendRequest($data)->assertOk();

        $this->assertDatabaseHas('voucher_codes', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_amount_must_match_the_exact_number_of_characters()
    {
        $data = VoucherCode::factory()->raw(['amount' => 20]);

        $this->signIn()->sendRequest($data)->assertJsonValidationErrors(['amount']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_type_must_be_one_of_the_predefined_discount_types()
    {
        $data = VoucherCode::factory()->raw(['type' => 4]);

        $this->signIn()->sendRequest($data)->assertJsonValidationErrors(['type']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.voucher_codes.create'), $data);
    }
}
