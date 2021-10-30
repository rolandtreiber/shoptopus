<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\CreatesApplication;
use Tests\TestCase;

/**
 * @group customers
 * @see \App\Http\Controllers\Admin\CustomerController
 */
class CustomerControllerTest extends TestCase
{
    use AdditionalAssertions, WithFaker, CreatesApplication;

    /**
     * @test
     */
    public function test_can_list_customers()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.customers', [
            'page' => 1,
            'paginate' => 20,
        ]));
        $customers = $response->json()['data'];
        $this->assertCount(18, $response->json()['data']);
        foreach ($customers as $customer) {
            $this->assertContains('customer', $customer['roles']);
        }
    }

    /**
     * @test
     */
    public function test_can_show_customer()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $customer = User::customers()->first();
        $response = $this->get(route('admin.api.show.customer', [
            'customer' => $customer->id
        ]));
        $response->assertJsonFragment([
                "id" => $customer->id,
                "avatar" => [
                    'url' => $customer->avatar->url,
                    'file_name' => $customer->avatar->file_name
                    ],
                "name" => $customer->name,
                "prefix" => $customer->prefix,
                "first_name" => $customer->first_name,
                "last_name" => $customer->last_name,
                "initials" => $customer->initials,
                "email" => $customer->email
        ]);
    }
}
