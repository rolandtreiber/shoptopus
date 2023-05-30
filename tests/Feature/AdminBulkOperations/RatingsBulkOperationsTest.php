<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group ratings-bulk-operations
 * @group bulk-operations
 */
class RatingsBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_set_multiple_ratings_verified(): void
    {
        $ratingIds = Rating::factory()->state([
            'verified' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-verified-status'), [
            'ids' => $ratingIds,
            'verified' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[0],
            'verified' => true,
        ]);
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[1],
            'verified' => true,
        ]);
    }

    /**
     * @test
     */
    public function test_can_set_multiple_ratings_unverified(): void
    {
        $ratingIds = Rating::factory()->state([
            'verified' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-verified-status'), [
            'ids' => $ratingIds,
            'verified' => false,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[0],
            'verified' => false,
        ]);
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[1],
            'verified' => false,
        ]);
    }

    /**
     * @test
     */
    public function test_can_enable_multiple_ratings(): void
    {
        $ratingIds = Rating::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-availability'), [
            'ids' => $ratingIds,
            'availability' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[0],
            'enabled' => true,
        ]);
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[1],
            'enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_ratings(): void
    {
        $ratingIds = Rating::factory()->state([
            'enabled' => true,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-availability'), [
            'ids' => $ratingIds,
            'availability' => false,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[0],
            'enabled' => false,
        ]);
        $this->assertDatabaseHas('ratings', [
            'id' => $ratingIds[1],
            'enabled' => false,
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_authorization(): void
    {
        $ratingIds = Rating::factory()->state([
            'verified' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.ratings.bulk.update-verified-status'), [
            'ids' => $ratingIds,
            'verified' => true,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_authentication(): void
    {
        $ratingIds = Rating::factory()->state([
            'verified' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.ratings.bulk.update-verified-status'), [
            'ids' => $ratingIds,
            'verified' => true,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_not_found_handled(): void
    {
        $ratingIds = Rating::factory()->state([
            'verified' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-verified-status'), [
            'ids' => [...$ratingIds, 'invalid id'],
            'verified' => true,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_validation(): void
    {
        $ratingIds = Rating::factory()->state([
            'verified' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-verified-status'), [
            'ids' => $ratingIds,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_authorization(): void
    {
        $ratingIds = Rating::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->post(route('admin.api.ratings.bulk.update-availability'), [
            'ids' => $ratingIds,
            'availability' => true,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_authentication(): void
    {
        $ratingIds = Rating::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $response = $this->post(route('admin.api.ratings.bulk.update-availability'), [
            'ids' => $ratingIds,
            'availability' => true,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_not_found_handled(): void
    {
        $ratingIds = Rating::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-availability'), [
            'ids' => [...$ratingIds, 'invalid id'],
            'availability' => true,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_validation(): void
    {
        $ratingIds = Rating::factory()->state([
            'enabled' => false,
        ])->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.api.ratings.bulk.update-availability'), [
            'ids' => $ratingIds,
        ]);
        $response->assertStatus(422);
    }
}
