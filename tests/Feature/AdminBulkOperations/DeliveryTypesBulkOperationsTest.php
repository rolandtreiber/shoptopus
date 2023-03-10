<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Models\DeliveryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group delivery-types-bulk-operations
 * @group bulk-operations
 */
class DeliveryTypesBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_enable_multiple_delivery_types()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.delivery-types.bulk.update-availability'), [
            'ids' => $deliveryTypeIds,
            'availability' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('delivery_types', [
            'id' => $deliveryTypeIds[0],
            'enabled' => 1,
        ]);
        $this->assertDatabaseHas('delivery_types', [
            'id' => $deliveryTypeIds[1],
            'enabled' => 1,
        ]);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_delivery_types()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.delivery-types.bulk.update-availability'), [
            'ids' => $deliveryTypeIds,
            'availability' => false,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('delivery_types', [
            'id' => $deliveryTypeIds[0],
            'enabled' => 0,
        ]);
        $this->assertDatabaseHas('delivery_types', [
            'id' => $deliveryTypeIds[1],
            'enabled' => 0,
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_delivery_types()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.delivery-types.bulk.delete'), [
            'ids' => $deliveryTypeIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('delivery_types', [
            'id' => $deliveryTypeIds[0],
        ]);
        $this->assertSoftDeleted('delivery_types', [
            'id' => $deliveryTypeIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_validation()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.delivery-types.bulk.update-availability'), [
            'ids' => $deliveryTypeIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_authorization()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.delivery-types.bulk.update-availability'), [
            'ids' => $deliveryTypeIds,
            'availability' => false,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_authentication()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.delivery-types.bulk.update-availability'), [
            'ids' => $deliveryTypeIds,
            'availability' => false,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_not_found_handled()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.delivery-types.bulk.update-availability'), [
            'ids' => [...$deliveryTypeIds, 'invalid id'],
            'availability' => false,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_validation()
    {
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.delivery-types.bulk.delete'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_authorization()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.delivery-types.bulk.delete'), [
            'ids' => $deliveryTypeIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_authentication()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.delivery-types.bulk.delete'), [
            'ids' => $deliveryTypeIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_not_found_handled()
    {
        $deliveryTypeIds = DeliveryType::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.delivery-types.bulk.delete'), [
            'ids' => [...$deliveryTypeIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }
}
