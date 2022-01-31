<?php

namespace Tests\PublicApi\Address;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteAddressTest extends TestCase
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
     * @group apiDelete
     */
    public function unauthorised_users_are_not_allowed_to_delete_addresses()
    {
        $unAuthenticatedRes = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);

        $unAuthorisedRes = $this->signIn()->sendRequest()->json();

        $this->assertEquals('This action is unauthorized.', $unAuthorisedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthorisedRes['user_message']);
    }

    /**
     * @test
     * @group apiDelete
     */
    public function authorised_users_can_delete_their_addresses()
    {
        $user = User::factory()->create();

        $this->address->update(['user_id' => $user->id]);

        $this->assertDatabaseHas('addresses', [
            'id' => $this->address->id,
            'deleted_at' => null
        ]);

        $this->signIn($user)->sendRequest()->assertOk();

        $this->assertDatabaseHas('addresses', [
            'id' => $this->address->id,
            'deleted_at' => now()
        ]);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('api.addresses.delete', ['id' => $this->address->id]), $data);
    }
}
