<?php

namespace Tests\PublicApi\Address;

use Tests\TestCase;
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
        $data = [
            'address_line_1' => '10A Couzens Place',
            'town' => 'Bristol',
            'post_code' => 'BS34 8PL',
            'country' => 'UK',
            'name' => 'home'
        ];

        $this->signIn($this->address->user)->sendRequest($data);

        $this->assertDatabaseHas('addresses', array_merge($data, ['user_id' => $this->address->user->id]));
    }

    /**
     * @test
     * @group apiPatch
     */
    public function the_longitude_and_latitude_must_match_the_exact_number_of_characters()
    {
        $data = Address::factory()->raw([
            'lat' => 2000,
            'lon' => 2000.768
        ]);

        $this->signIn($this->address->user)->sendRequest($data)->assertJsonValidationErrors(['lat', 'lon']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('api.addresses.update', ['id' => $this->address->id]), $data);
    }
}
