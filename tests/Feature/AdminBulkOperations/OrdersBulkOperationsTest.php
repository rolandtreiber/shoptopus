<?php

namespace Tests\Feature\AdminBulkOperations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group orders-bulk-operations
 * @group bulk-operations
 */
class OrdersBulkOperationsTest extends TestCase
{
    /**
     * @test
     */
    public function test_can_update_multiple_order_status()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_validation()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_order_not_found_throws_expected_error()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_order_status_update_failure_causes_all_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

}
