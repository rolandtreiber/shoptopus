<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Enums\Interval;
use App\Models\VoucherCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group voucher-codes-bulk-operations
 * @group bulk-operations
 */
class VoucherCodesBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    private Carbon $validFrom;

    private Carbon $validUntil;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function test_can_expire_multiple_voucher_codes(): void
    {
        $this->validFrom = Carbon::now()->subDay();
        $this->validUntil = Carbon::now()->addMonth();

        $voucherCodeIds = VoucherCode::factory()->state([
            'valid_from' => $this->validFrom,
            'valid_until' => $this->validUntil,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.expire'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[0],
            'valid_until' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[1],
            'valid_until' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_start_multiple_voucher_codes(): void
    {
        $this->validFrom = Carbon::now()->subDay();
        $this->validUntil = Carbon::now()->addMonth();

        $voucherCodeIds = VoucherCode::factory()->state([
            'valid_from' => $this->validFrom->addMonth(),
            'valid_until' => $this->validUntil->addMonths(3),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.start'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_make_active_for_one_day_multiple_voucher_codes(): void
    {
        $this->validFrom = Carbon::now()->subDay();
        $this->validUntil = Carbon::now()->addMonth();

        $voucherCodeIds = VoucherCode::factory()->state([
            'valid_from' => $this->validFrom->subMonths(2),
            'valid_until' => $this->validUntil->subMonth(),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.activate-for-period'), [
            'ids' => $voucherCodeIds,
            'period' => Interval::Day,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addDay()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addDay()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_make_active_for_one_week_multiple_voucher_codes(): void
    {
        $this->validFrom = Carbon::now()->subDay();
        $this->validUntil = Carbon::now()->addMonth();

        $voucherCodeIds = VoucherCode::factory()->state([
            'valid_from' => $this->validFrom->subMonths(2),
            'valid_until' => $this->validUntil->subMonth(),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.activate-for-period'), [
            'ids' => $voucherCodeIds,
            'period' => Interval::Week,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addWeek()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addWeek()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_make_active_for_one_month_multiple_voucher_codes(): void
    {
        $this->validFrom = Carbon::now()->subDay();
        $this->validUntil = Carbon::now()->addMonth();

        $voucherCodeIds = VoucherCode::factory()->state([
            'valid_from' => $this->validFrom->subMonths(2),
            'valid_until' => $this->validUntil->subMonth(),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.activate-for-period'), [
            'ids' => $voucherCodeIds,
            'period' => Interval::Month,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addMonth()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('voucher_codes', [
            'id' => $voucherCodeIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addMonth()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_voucher_codes(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.voucher-codes.bulk.delete'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('voucher_codes', [
            'id' => $voucherCodeIds[0],
        ]);
        $this->assertSoftDeleted('voucher_codes', [
            'id' => $voucherCodeIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_delete_authorization(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.voucher-codes.bulk.delete'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_delete_authentication(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.voucher-codes.bulk.delete'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_delete_not_found_handled(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.voucher-codes.bulk.delete'), [
            'ids' => [...$voucherCodeIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_delete_validation(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.voucher-codes.bulk.delete'), [
            'ids' => [...$voucherCodeIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_make_active_authorization(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.voucher-codes.bulk.activate-for-period'), [
            'ids' => $voucherCodeIds,
            'period' => Interval::Month,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_make_active_authentication(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.voucher-codes.bulk.activate-for-period'), [
            'ids' => $voucherCodeIds,
            'period' => Interval::Month,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_make_active_not_found_handled(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.activate-for-period'), [
            'ids' => [...$voucherCodeIds, 'invalid id'],
            'period' => Interval::Month,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_make_active_validation(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.activate-for-period'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_start_authorization(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.voucher-codes.bulk.start'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_start_authentication(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.voucher-codes.bulk.start'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_start_not_found_handled(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.start'), [
            'ids' => [...$voucherCodeIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_start_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.start'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_expire_authorization(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.voucher-codes.bulk.expire'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_expire_authentication(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.voucher-codes.bulk.expire'), [
            'ids' => $voucherCodeIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_expire_not_found_handled(): void
    {
        $voucherCodeIds = VoucherCode::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.expire'), [
            'ids' => [...$voucherCodeIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_voucher_codes_expire_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.voucher-codes.bulk.expire'), []);
        $response->assertStatus(422);
    }
}
