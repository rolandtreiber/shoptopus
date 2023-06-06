<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\AdminControllerTestCase;
use Tests\CreatesApplication;

/**
 * @group admin-base-crud
 * @group customers
 *
 * @see \App\Http\Controllers\Admin\CustomerController
 */
class CustomerControllerTest extends AdminControllerTestCase
{
    use AdditionalAssertions, WithFaker, CreatesApplication, RefreshDatabase;

    /**
     * @test
     */
    public function test_can_list_customers(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.customers', [
            'page' => 1,
            'paginate' => 100,
        ]));
        $customers = $response->json()['data'];
        $this->assertCount(18, $response->json()['data']);
    }

    /**
     * @test
     */
    public function test_can_show_customer(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $customer = User::customers()->first();
        $response = $this->get(route('admin.api.show.customer', [
            'customer' => $customer->id,
        ]));
        $response->assertJsonFragment([
            'id' => $customer->id,
            'avatar' => [
                'url' => $customer->avatar->url,
                'file_name' => $customer->avatar->file_name,
            ],
            'name' => $customer->name,
            'prefix' => $customer->prefix,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'initials' => $customer->initials,
            'email' => $customer->email,
        ]);
    }
}
