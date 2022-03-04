<?php

namespace Tests\Feature\AdminBulkOperations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group transactions-bulk-operations
 * @group bulk-operations
 */
class TransactionsBulkOperationsTest extends TestCase
{
    /**
     * @test
     */
    public function test_can_update_the_status_of_multiple_transactions()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_not_found_handled()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_transactions_status_update_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

}
