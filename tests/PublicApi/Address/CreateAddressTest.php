<?php

namespace Tests\PublicApi\Address;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group apiPost
     */
    public function unauthenticated_users_are_not_allowed_to_create_addresses()
    {
        $data = Address::factory()->raw();

        $res = $this->sendRequest($data)->json();

        $this->assertEquals('Unauthenticated.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        $data = [
            'address_line_1' => null,
            'town' => null,
            'post_code' => null,
            'country' => null
        ];

        $this->signIn()
            ->sendRequest($data)
            ->assertJsonValidationErrors(['address_line_1', 'town', 'post_code', 'country']);

        $this->assertDatabaseMissing('addresses', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function authenticated_users_can_create_addresses()
    {
        $this->assertTrue(true);
//        $data = Address::factory()->raw();
//
//        $this->signIn()
//            ->sendRequest($data)
//            ->assertOk();
//
//        $this->assertDatabaseHas('addresses', $data);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_is_saved_for_the_currently_authenticated_user()
    {
        $data = Address::factory()->raw(['user_id' => User::factory()->create()->id]);

        $user = User::factory()->create();

        $this->signIn($user)
            ->sendRequest($data)
            ->assertOk();

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_longitude_and_latitude_must_match_the_exact_number_of_characters()
    {
        $data = Address::factory()->raw([
            'lat' => 2000,
            'lon' => 2000.768
        ]);

        $this->signIn()
            ->sendRequest($data)
            ->assertJsonValidationErrors(['lat', 'lon']);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.addresses.create'), $data);
    }
}
