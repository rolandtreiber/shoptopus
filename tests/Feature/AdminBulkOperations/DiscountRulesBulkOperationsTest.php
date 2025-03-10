<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Enums\Interval;
use App\Models\DiscountRule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group discount-rules-bulk-operations
 * @group bulk-operations
 */
class DiscountRulesBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    private Carbon $validFrom;

    private Carbon $validUntil;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validFrom = Carbon::now()->subDay();
        $this->validUntil = Carbon::now()->addMonth();
    }

    /**
     * @test
     */
    public function test_can_expire_multiple_discount_rules(): void
    {
        $discountRuleIds = DiscountRule::factory()->state([
            'valid_from' => $this->validFrom,
            'valid_until' => $this->validUntil,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.expire'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[0],
            'valid_until' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[1],
            'valid_until' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_start_multiple_discount_rules(): void
    {
        $discountRuleIds = DiscountRule::factory()->state([
            'valid_from' => $this->validFrom->addMonth(),
            'valid_until' => $this->validUntil->addMonths(3),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.start'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_make_active_for_one_day_multiple_discount_rules(): void
    {
        $discountRuleIds = DiscountRule::factory()->state([
            'valid_from' => $this->validFrom->subMonths(2),
            'valid_until' => $this->validUntil->subMonth(),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.activate-for-period'), [
            'ids' => $discountRuleIds,
            'period' => Interval::Day,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addDay()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addDay()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_make_active_for_one_week_multiple_discount_rules(): void
    {
        $discountRuleIds = DiscountRule::factory()->state([
            'valid_from' => $this->validFrom->subMonths(2),
            'valid_until' => $this->validUntil->subMonth(),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.activate-for-period'), [
            'ids' => $discountRuleIds,
            'period' => Interval::Week,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addWeek()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addWeek()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_make_active_for_one_month_multiple_discount_rules(): void
    {
        $discountRuleIds = DiscountRule::factory()->state([
            'valid_from' => $this->validFrom->subMonths(2),
            'valid_until' => $this->validUntil->subMonth(),
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.activate-for-period'), [
            'ids' => $discountRuleIds,
            'period' => Interval::Month,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[0],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addMonth()->format('Y-m-d H:i:s'),
        ]);
        $this->assertDatabaseHas('discount_rules', [
            'id' => $discountRuleIds[1],
            'valid_from' => Carbon::now()->format('Y-m-d H:i:s'),
            'valid_until' => Carbon::now()->endOfDay()->addMonth()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_discount_rules(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.discount-rules.bulk.delete'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('discount_rules', [
            'id' => $discountRuleIds[0],
        ]);
        $this->assertSoftDeleted('discount_rules', [
            'id' => $discountRuleIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_delete_authorization(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.discount-rules.bulk.delete'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_delete_authentication(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.discount-rules.bulk.delete'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_delete_not_found_handled(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.discount-rules.bulk.delete'), [
            'ids' => [...$discountRuleIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_delete_validation(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.discount-rules.bulk.delete'), [
            'ids' => [...$discountRuleIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_make_active_authorization(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.discount-rules.bulk.activate-for-period'), [
            'ids' => $discountRuleIds,
            'period' => Interval::Month,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_make_active_authentication(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.discount-rules.bulk.activate-for-period'), [
            'ids' => $discountRuleIds,
            'period' => Interval::Month,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_make_active_not_found_handled(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.activate-for-period'), [
            'ids' => [...$discountRuleIds, 'invalid id'],
            'period' => Interval::Month,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_make_active_validation(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.activate-for-period'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_start_authorization(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.discount-rules.bulk.start'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_start_authentication(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.discount-rules.bulk.start'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_start_not_found_handled(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.start'), [
            'ids' => [...$discountRuleIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_start_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.start'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_expire_authorization(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.discount-rules.bulk.expire'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_expire_authentication(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.discount-rules.bulk.expire'), [
            'ids' => $discountRuleIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_expire_not_found_handled(): void
    {
        $discountRuleIds = DiscountRule::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.expire'), [
            'ids' => [...$discountRuleIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_discount_rules_expire_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.discount-rules.bulk.expire'), []);
        $response->assertStatus(422);
    }
}
