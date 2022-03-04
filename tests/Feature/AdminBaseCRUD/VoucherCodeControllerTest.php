<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Enums\DiscountTypes;
use App\Models\User;
use App\Models\VoucherCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group voucher_codes
 * @see \App\Http\Controllers\Admin\VoucherCodeController
 */
class VoucherCodeControllerTest extends AdminControllerTestCase
{
    /**
     * @test
     */
    public function test_voucher_codes_can_be_listed()
    {
        $voucherCodes = VoucherCode::factory()->count(3)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.voucher-codes', [
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $voucherCodes[0]->id
        ]);
        $response->assertJsonFragment([
            'id' => $voucherCodes[1]->id
        ]);
        $response->assertJsonFragment([
            'id' => $voucherCodes[2]->id
        ]);
    }

    /**
     * @test
     */
    public function test_voucher_code_can_be_shown()
    {
        $voucherCode = VoucherCode::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.voucher-code', [
            'voucherCode' => $voucherCode->id,
        ]));
        $response->assertJsonFragment([
            'id' => $voucherCode->id
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.id', $voucherCode->id)
                ->where('data.amount', $voucherCode->amount)
                ->where('data.code', $voucherCode->code)
                ->etc());
    }

    /**
     * @test
     */
    public function test_voucher_code_can_be_created()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();

        $response = $this->post(route('admin.api.create.voucher-code'), [
            'amount' => 10,
            'type' => DiscountTypes::Percentage,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertCreated();
        $voucherCodeId = $response->json()['data']['id'];
        $code = VoucherCode::find($voucherCodeId);
        $this->assertEquals(10, $code->amount);
        $this->assertEquals(DiscountTypes::Percentage, $code->type);
        $this->assertEquals($validFrom->format('Y-m-d H:i:s'), $code->valid_from);
        $this->assertEquals($validUntil->format('Y-m-d H:i:s'), $code->valid_until);
    }

    /**
     * @test
     */
    public function test_voucher_code_can_be_updated()
    {
        $voucherCode = VoucherCode::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addWeek();
        $response = $this->patch(route('admin.api.update.voucher-code', [
            'voucherCode' => $voucherCode
        ]), [
            'amount' => 5,
            'type' => DiscountTypes::Amount,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertOk();
        $voucherCodeId = $response->json()['data']['id'];
        $code = VoucherCode::find($voucherCodeId);
        $this->assertEquals(5, $code->amount);
        $this->assertEquals(DiscountTypes::Amount, $code->type);
        $this->assertEquals($validFrom->format('Y-m-d H:i:s'), $code->valid_from);
        $this->assertEquals($validUntil->format('Y-m-d H:i:s'), $code->valid_until);
    }

    /**
     * @test
     */
    public function test_voucher_code_can_be_deleted()
    {
        $voucherCode = VoucherCode::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.voucher-code', $voucherCode));

        $this->assertSoftDeleted($voucherCode);
    }

    /**
     * @test
     */
    public function test_voucher_code_creation_requires_appropriate_permissions()
    {
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->post(route('admin.api.create.voucher-code'), [
            'amount' => 10,
            'type' => DiscountTypes::Percentage,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_voucher_code_updating_requires_appropriate_permissions()
    {
        $voucherCode = VoucherCode::factory()->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addWeek();
        $response = $this->patch(route('admin.api.update.voucher-code', [
            'voucherCode' => $voucherCode
        ]), [
            'amount' => 5,
            'type' => DiscountTypes::Amount,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_voucher_code_deletion_requires_appropriate_permissions()
    {
        $voucherCode = VoucherCode::factory()->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->delete(route('admin.api.delete.voucher-code', $voucherCode));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_voucher_code_creation_validation()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();

        $response = $this->post(route('admin.api.create.voucher-code'), [
            'type' => DiscountTypes::Percentage,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_voucher_code_update_validation()
    {
        $voucherCode = VoucherCode::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addWeek();
        $response = $this->patch(route('admin.api.update.voucher-code', [
            'voucherCode' => $voucherCode
        ]), [
            'amount' => 'thirtytwo',
            'type' => DiscountTypes::Amount,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertStatus(422);
    }
}
