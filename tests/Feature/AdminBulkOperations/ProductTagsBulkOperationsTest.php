<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Models\ProductTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group product-tags-bulk-operations
 * @group bulk-operations
 */
class ProductTagsBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_enable_multiple_product_tags()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-tags.bulk.update-availability'), [
            'ids' => $productTagIds,
            'availability' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('product_tags', [
            'id' => $productTagIds[0],
            'enabled' => 1,
        ]);
        $this->assertDatabaseHas('product_tags', [
            'id' => $productTagIds[1],
            'enabled' => 1,
        ]);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_product_tags()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-tags.bulk.update-availability'), [
            'ids' => $productTagIds,
            'availability' => false,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('product_tags', [
            'id' => $productTagIds[0],
            'enabled' => 0,
        ]);
        $this->assertDatabaseHas('product_tags', [
            'id' => $productTagIds[1],
            'enabled' => 0,
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_product_tags()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-tags.bulk.delete'), [
            'ids' => $productTagIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('product_tags', [
            'id' => $productTagIds[0],
        ]);
        $this->assertSoftDeleted('product_tags', [
            'id' => $productTagIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_availability_update_validation()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-tags.bulk.update-availability'), [
            'ids' => $productTagIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_availability_update_authorization()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.product-tags.bulk.update-availability'), [
            'ids' => $productTagIds,
            'availability' => false,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_availability_update_authentication()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.product-tags.bulk.update-availability'), [
            'ids' => $productTagIds,
            'availability' => false,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_availability_update_not_found_handled()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.product-tags.bulk.update-availability'), [
            'ids' => [...$productTagIds, 'invalid id'],
            'availability' => false,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_delete_validation()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-tags.bulk.delete'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_delete_authorization()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.product-tags.bulk.delete'), [
            'ids' => $productTagIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_delete_authentication()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.product-tags.bulk.delete'), [
            'ids' => $productTagIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_product_tags_delete_not_found_handled()
    {
        $productTagIds = ProductTag::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.product-tags.bulk.delete'), [
            'ids' => [...$productTagIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }
}
