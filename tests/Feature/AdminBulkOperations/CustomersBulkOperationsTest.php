<?php

namespace Tests\Feature\AdminBulkOperations;

use App\Mail\Admin\GenericAdminEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\BulkOperationsTestCase;

/**
 * @group customers-bulk-operations
 * @group bulk-operations
 */
class CustomersBulkOperationsTest extends BulkOperationsTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_can_send_email_to_multiple_users()
    {
        Mail::fake();
        $emailAddresses = User::factory()->count(2)->create()->pluck('email');
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.customers.send-email'), [
            'addresses' => $emailAddresses,
        ]);

        $response->assertOk();
        Mail::assertSent(GenericAdminEmail::class, 2);
    }

    /**
     * @test
     */
    public function test_email_archives_created()
    {
        Mail::fake();
        $emailAddresses = User::factory()->count(2)->create()->pluck('email');
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.customers.send-email'), [
            'addresses' => $emailAddresses,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('email_archives', [
            'address' => $emailAddresses[0],
        ]);
        $this->assertDatabaseHas('email_archives', [
            'address' => $emailAddresses[1],
        ]);
    }

    /**
     * @test
     */
    public function test_bulk_email_validation()
    {
        Mail::fake();
        $emailAddresses = User::factory()->count(2)->create()->pluck('email');
        $this->signIn($this->superAdmin);
        $response = $this->post(route('admin.customers.send-email'), [
            'addresses' => [...$emailAddresses, 'invalid email address'],
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_bulk_email_authentication()
    {
        Mail::fake();
        $emailAddresses = User::factory()->count(2)->create()->pluck('email');
        $response = $this->post(route('admin.customers.send-email'), [
            'addresses' => $emailAddresses,
        ]);

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_bulk_email_authorization()
    {
        Mail::fake();
        $emailAddresses = User::factory()->count(2)->create()->pluck('email');
        $this->signIn(User::factory()->create());
        $response = $this->post(route('admin.customers.send-email'), [
            'addresses' => $emailAddresses,
        ]);

        $response->assertForbidden();
    }
}
