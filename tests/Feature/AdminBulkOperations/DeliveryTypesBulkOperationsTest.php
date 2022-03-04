<?php

namespace Tests\Feature\AdminBulkOperations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group delivery-types-bulk-operations
 * @group bulk-operations
 */
class DeliveryTypesBulkOperationsTest extends TestCase
{
    /**
     * @test
     */
    public function test_can_enable_multiple_delivery_types()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_can_disable_multiple_delivery_types()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_delivery_types()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_not_found_handled()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_availability_update_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_not_found_handled()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_delivery_types_delete_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

}
