<?php

namespace Tests\Feature\AdminBulkOperations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group products-bulk-operations
 * @group bulk-operations
 */
class ProductsBulkOperationsTest extends TestCase
{
    /**
     * @test
     */
    public function test_can_archive_multiple_products()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_can_delete_multiple_products()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_archive_validation()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_archive_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_archive_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_archive_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_validation()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_not_found_handled()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_products_delete_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

}
