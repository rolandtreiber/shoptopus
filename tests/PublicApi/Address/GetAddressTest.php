<?php

namespace Tests\PublicApi\Address;

use Tests\TestCase;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\Address\AddressRepository;

class GetAddressTest extends TestCase
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
     * @group apiGet
     */
    public function unauthenticated_users_are_not_allowed_to_get_addresses()
    {
        $unAuthenticatedRes = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);

        $res = $this->signIn()->sendRequest()->json();

        $this->assertEquals('This action is unauthorized.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_can_return_an_address_by_its_id()
    {
        $this->signIn($this->address->user)
            ->sendRequest()
            ->assertOk()
            ->assertSee($this->address->address_line_1);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_all_required_fields()
    {
        $this->signIn($this->address->user)
            ->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    app()->make(AddressRepository::class)->getSelectableColumns(false)
                ]
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_owner_of_the_address()
    {
        $this->signIn($this->address->user)
            ->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    [
                        'user' => [
                            'id',
                            'first_name',
                            'last_name',
                            'email',
                            'name',
                            'initials',
                            'prefix',
                            'phone',
                            'avatar',
                            'email_verified_at',
                            'client_ref',
                            'temporary',
                            'is_favorite'
                        ]
                    ]
                ]
            ]);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.addresses.get', ['id' => $this->address->id]));
    }
}
