<?php

namespace Tests\PublicApi\Address;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateAddressTest extends TestCase
{
    use RefreshDatabase;

    protected $address;

    public function setUp() : void
    {
        parent::setUp();

        $this->address = Address::factory()->create();
    }

    /**
     * @test
     * @group apiPatch
     */
    public function unauthorised_users_are_not_allowed_to_update_addresses()
    {
        $data = Address::factory()->raw();

        $unAuthenticatedRes = $this->sendRequest($data)->json();

        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);

        $unAuthorisedRes = $this->signIn()->sendRequest($data)->json();

        $this->assertEquals('This action is unauthorized.', $unAuthorisedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthorisedRes['user_message']);
    }

    /**
     * @test
     * @group apiPatch
     */
    public function authorised_users_can_update_their_addresses()
    {
        $user = User::factory()->create();

        $this->address->update(['user_id' => $user->id]);

        $data = Address::factory()->raw();

        $this->signIn($user)
            ->sendRequest($data)
            ->assertOk();

        $this->assertDatabaseHas('addresses', $data);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('api.addresses.update', ['id' => $this->address->id]), $data);
    }
}
