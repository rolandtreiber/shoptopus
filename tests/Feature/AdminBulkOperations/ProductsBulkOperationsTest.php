<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group products-bulk-operations
 * @group bulk-operations
 */
class ProductsBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_archive_multiple_products()
    {
        $productIds = Product::factory()->state([
            'status' => ProductStatus::Active,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.products.bulk.archive'), [
            'ids' => $productIds,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('products', [
            'id' => $productIds[0],
            'status' => ProductStatus::Discontinued,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $productIds[1],
            'status' => ProductStatus::Discontinued,
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_products()
    {
        $productIds = Product::factory()->state([
            'status' => ProductStatus::Active,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.products.bulk.delete'), [
            'ids' => $productIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('products', [
            'id' => $productIds[0],
        ]);
        $this->assertSoftDeleted('products', [
            'id' => $productIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_products_archive_validation()
    {
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.products.bulk.archive'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_products_archive_authorization()
    {
        $productIds = Product::factory()->state([
            'status' => ProductStatus::Active,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.products.bulk.archive'), [
            'ids' => $productIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_products_archive_authentication()
    {
        $productIds = Product::factory()->state([
            'status' => ProductStatus::Active,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.products.bulk.archive'), [
            'ids' => $productIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_validation()
    {
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.products.bulk.delete'), [
            'ids' => ['1', '2'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_authorization()
    {
        $productIds = Product::factory()->state([
            'status' => ProductStatus::Active,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.products.bulk.delete'), [
            'ids' => $productIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_authentication()
    {
        $productIds = Product::factory()->state([
            'status' => ProductStatus::Active,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.products.bulk.delete'), [
            'ids' => $productIds,
        ]);
        $response->assertStatus(500);
    }
}
