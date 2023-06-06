<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group banners-bulk-operations
 * @group bulk-operations
 */
class BannersBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_enable_multiple_banners(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.banners.bulk.update-availability'), [
            'ids' => $bannerIds,
            'availability' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('banners', [
            'id' => $bannerIds[0],
            'enabled' => 1,
        ]);
        $this->assertDatabaseHas('banners', [
            'id' => $bannerIds[1],
            'enabled' => 1,
        ]);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_banners(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.banners.bulk.update-availability'), [
            'ids' => $bannerIds,
            'availability' => false,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('banners', [
            'id' => $bannerIds[0],
            'enabled' => 0,
        ]);
        $this->assertDatabaseHas('banners', [
            'id' => $bannerIds[1],
            'enabled' => 0,
        ]);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_banners(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.banners.bulk.delete'), [
            'ids' => $bannerIds,
        ]);
        $response->assertOk();
        $this->assertSoftDeleted('banners', [
            'id' => $bannerIds[0],
        ]);
        $this->assertDatabaseHas('banners', [
            'id' => $bannerIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_banners_availability_update_validation(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.banners.bulk.update-availability'), [
            'ids' => $bannerIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_banners_availability_update_authorization(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.banners.bulk.update-availability'), [
            'ids' => $bannerIds,
            'availability' => false,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_banners_availability_update_authentication(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.banners.bulk.update-availability'), [
            'ids' => $bannerIds,
            'availability' => false,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_banners_availability_update_not_found_handled(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.banners.bulk.update-availability'), [
            'ids' => [...$bannerIds, 'invalid id'],
            'availability' => false,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_banners_delete_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.banners.bulk.delete'), []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_banners_delete_authorization(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.banners.bulk.delete'), [
            'ids' => $bannerIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_banners_delete_authentication(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.banners.bulk.delete'), [
            'ids' => $bannerIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_banners_delete_not_found_handled(): void
    {
        $bannerIds = Banner::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.banners.bulk.delete'), [
            'ids' => [...$bannerIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }
}
