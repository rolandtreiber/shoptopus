<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Enums\OrderStatuses;
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
    public function test_can_update_multiple_order_status()
    {
        $orderIds = Order::factory()->state([
            'status' => OrderStatuses::Paid
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => $orderIds,
            'status' => OrderStatuses::InTransit
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('orders', [
            'id' => $orderIds[0],
            'status' => OrderStatuses::InTransit
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $orderIds[1],
            'status' => OrderStatuses::InTransit
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_validation()
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'status' => OrderStatuses::InTransit
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_authorization()
    {
        $orderIds = Order::factory()->state([
            'status' => OrderStatuses::Paid
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => $orderIds,
            'status' => OrderStatuses::InTransit
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_authentication()
    {
        $orderIds = Order::factory()->state([
            'status' => OrderStatuses::Paid
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => $orderIds,
            'status' => OrderStatuses::InTransit
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_order_not_found_throws_expected_error()
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.orders.bulk.status-update'), [
            'ids' => ['4321421', '5534643'],
            'status' => OrderStatuses::InTransit
        ]);
        $response->assertStatus(422);
    }
}
