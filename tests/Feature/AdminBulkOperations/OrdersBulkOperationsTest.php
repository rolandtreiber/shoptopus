<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group orders-bulk-operations
 * @group bulk-operations
 */
class OrdersBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_update_multiple_order_status(): void
    {
        $orderIds = Order::factory()->state([
            'status' => OrderStatus::Paid,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => $orderIds,
            'status' => OrderStatus::InTransit,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('orders', [
            'id' => $orderIds[0],
            'status' => OrderStatus::InTransit,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $orderIds[1],
            'status' => OrderStatus::InTransit,
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'status' => OrderStatus::InTransit,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_authorization(): void
    {
        $orderIds = Order::factory()->state([
            'status' => OrderStatus::Paid,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => $orderIds,
            'status' => OrderStatus::InTransit,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_authentication(): void
    {
        $orderIds = Order::factory()->state([
            'status' => OrderStatus::Paid,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => $orderIds,
            'status' => OrderStatus::InTransit,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_order_not_found_throws_expected_error(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => ['4321421', '5534643'],
            'status' => OrderStatus::InTransit,
        ]);
        $response->assertStatus(422);
    }
}
