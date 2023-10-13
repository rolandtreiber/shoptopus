<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Models\ProductAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group product-attributes-bulk-operations
 * @group bulk-operations
 */
class ProductAttributesBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_enable_multiple_product_attributes(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-attributes.bulk.update-availability'), [
            'ids' => $productAttributeIds,
            'availability' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('product_attributes', [
            'id' => $productAttributeIds[0],
            'enabled' => 1,
        ]);
        $this->assertDatabaseHas('product_attributes', [
            'id' => $productAttributeIds[1],
            'enabled' => 1,
        ]);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_product_attributes(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-attributes.bulk.update-availability'), [
            'ids' => $productAttributeIds,
            'availability' => false,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('product_attributes', [
            'id' => $productAttributeIds[0],
            'enabled' => 0,
        ]);
        $this->assertDatabaseHas('product_attributes', [
            'id' => $productAttributeIds[1],
            'enabled' => 0,
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_product_attributes(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-attributes.bulk.delete'), [
            'ids' => $productAttributeIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('product_attributes', [
            'id' => $productAttributeIds[0],
        ]);
        $this->assertSoftDeleted('product_attributes', [
            'id' => $productAttributeIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_availability_update_validation(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-attributes.bulk.update-availability'), [
            'ids' => $productAttributeIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_availability_update_authorization(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.product-attributes.bulk.update-availability'), [
            'ids' => $productAttributeIds,
            'availability' => false,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_availability_update_authentication(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.product-attributes.bulk.update-availability'), [
            'ids' => $productAttributeIds,
            'availability' => false,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_availability_update_not_found_handled(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-attributes.bulk.update-availability'), [
            'ids' => [...$productAttributeIds, 'invalid id'],
            'availability' => false,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_delete_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-attributes.bulk.delete'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_delete_authorization(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.product-attributes.bulk.delete'), [
            'ids' => $productAttributeIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_delete_authentication(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.product-attributes.bulk.delete'), [
            'ids' => $productAttributeIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_product_attributes_delete_not_found_handled(): void
    {
        $productAttributeIds = ProductAttribute::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-attributes.bulk.delete'), [
            'ids' => [...$productAttributeIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }
}
