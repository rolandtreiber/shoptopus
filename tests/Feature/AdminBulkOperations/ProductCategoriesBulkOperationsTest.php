<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group product-categories-bulk-operations
 * @group bulk-operations
 */
class ProductCategoriesBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_enable_multiple_product_categories(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-categories.bulk.update-availability'), [
            'ids' => $productCategoryIds,
            'availability' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('product_categories', [
            'id' => $productCategoryIds[0],
            'enabled' => 1,
        ]);
        $this->assertDatabaseHas('product_categories', [
            'id' => $productCategoryIds[1],
            'enabled' => 1,
        ]);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_product_categories(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-categories.bulk.update-availability'), [
            'ids' => $productCategoryIds,
            'availability' => false,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('product_categories', [
            'id' => $productCategoryIds[0],
            'enabled' => 0,
        ]);
        $this->assertDatabaseHas('product_categories', [
            'id' => $productCategoryIds[1],
            'enabled' => 0,
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_product_categories(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-categories.bulk.delete'), [
            'ids' => $productCategoryIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('product_categories', [
            'id' => $productCategoryIds[0],
        ]);
        $this->assertSoftDeleted('product_categories', [
            'id' => $productCategoryIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_availability_update_validation(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-categories.bulk.update-availability'), [
            'ids' => $productCategoryIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_availability_update_authorization(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.product-categories.bulk.update-availability'), [
            'ids' => $productCategoryIds,
            'availability' => false,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_availability_update_authentication(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.product-categories.bulk.update-availability'), [
            'ids' => $productCategoryIds,
            'availability' => false,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_availability_update_not_found_handled(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-categories.bulk.update-availability'), [
            'ids' => [...$productCategoryIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_delete_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-categories.bulk.delete'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_delete_authorization(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.product-categories.bulk.delete'), [
            'ids' => $productCategoryIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_delete_authentication(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.product-categories.bulk.delete'), [
            'ids' => $productCategoryIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_product_categories_delete_not_found_handled(): void
    {
        $productCategoryIds = ProductCategory::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-categories.bulk.delete'), [
            'ids' => [...$productCategoryIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }
}
