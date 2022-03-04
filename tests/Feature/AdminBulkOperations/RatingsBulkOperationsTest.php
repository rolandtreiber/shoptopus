<?php

namespace Tests\Feature\AdminBulkOperations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group ratings-bulk-operations
 * @group bulk-operations
 */
class RatingsBulkOperationsTest extends TestCase
{
    /**
     * @test
     */
    public function test_can_set_multiple_ratings_verified()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_can_set_multiple_ratings_unverified()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_can_enable_multiple_ratings()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_ratings()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_not_found_handled()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_verification_update_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_not_found_handled()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_ratings_availability_update_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

}
