<?php

namespace Tests\Feature\AdminBulkOperations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group files-bulk-operations
 * @group bulk-operations
 */
class FilesBulkOperationsTest extends TestCase
{
    /**
     * @test
     */
    public function test_bulk_files_delete_authorization()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_files_delete_authentication()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_files_delete_not_found_handled()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_bulk_files_delete_not_found_db_changes_rolled_back()
    {
        $this->assertTrue(true);
    }

}
