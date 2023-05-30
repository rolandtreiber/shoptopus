<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Models\FileContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BulkOperationsTestCase;

/**
 * @group files-bulk-operations
 * @group bulk-operations
 */
class FilesBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_multiple_files_can_be_deleted(): void
    {
        $fileContentIds = FileContent::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.files.bulk.delete'), [
            'ids' => $fileContentIds,
        ]);
        $response->assertOk();
        $this->assertDatabaseMissing('file_contents', [
            'id' => $fileContentIds[0],
        ]);
        $this->assertDatabaseMissing('file_contents', [
            'id' => $fileContentIds[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_files_delete_authorization(): void
    {
        $fileContentIds = FileContent::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->storeAssistant);
        $response = $this->delete(route('admin.api.files.bulk.delete'), [
            'ids' => $fileContentIds,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_bulk_files_delete_authentication(): void
    {
        $fileContentIds = FileContent::factory()->count(3)->create()->pluck('id')->toArray();
        $response = $this->delete(route('admin.api.files.bulk.delete'), [
            'ids' => $fileContentIds,
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_files_delete_not_found_handled(): void
    {
        $fileContentIds = FileContent::factory()->count(3)->create()->pluck('id')->toArray();
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.files.bulk.delete'), [
            'ids' => [...$fileContentIds, 'invalid id'],
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_files_delete_validation(): void
    {
        $this->signIn($this->superAdmin);
        $response = $this->delete(route('admin.api.files.bulk.delete'), []);
        $response->assertStatus(422);
    }
}
