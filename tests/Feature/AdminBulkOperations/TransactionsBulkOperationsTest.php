<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group transactions-bulk-operations
 * @group bulk-operations
 */
class TransactionsBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_update_the_status_of_multiple_transactions()
    {
        $transactionIds = Payment::factory()->state([
            'status' => PaymentStatus::Pending,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.payments.bulk.status-update'), [
            'ids' => $transactionIds,
            'status' => PaymentStatus::Settled,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('payments', [
            'id' => $transactionIds[0],
            'status' => PaymentStatus::Settled,
        ]);
        $this->assertDatabaseHas('payments', [
            'id' => $transactionIds[1],
            'status' => PaymentStatus::Settled,
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_authorization()
    {
        $transactionIds = Payment::factory()->state([
            'status' => PaymentStatus::Pending,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.payments.bulk.status-update'), [
            'ids' => $transactionIds,
            'status' => PaymentStatus::Settled,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_authentication()
    {
        $transactionIds = Payment::factory()->state([
            'status' => PaymentStatus::Pending,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.payments.bulk.status-update'), [
            'ids' => $transactionIds,
            'status' => PaymentStatus::Settled,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_not_found_handled()
    {
        $transactionIds = Payment::factory()->state([
            'status' => PaymentStatus::Pending,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeManager);
        $response = $this->post(route('admin.api.payments.bulk.status-update'), [
            'ids' => [...$transactionIds, 'invalid id'],
            'status' => PaymentStatus::Settled,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_validation()
    {
        $transactionIds = Payment::factory()->state([
            'status' => PaymentStatus::Pending,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeManager);
        $response = $this->post(route('admin.api.payments.bulk.status-update'), [
            'ids' => $transactionIds,
        ]);
        $response->assertStatus(422);
    }
}
