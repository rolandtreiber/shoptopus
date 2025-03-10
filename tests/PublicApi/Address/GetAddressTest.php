<?php

namespace Tests\PublicApi\Address;

use App\Models\Address;
use App\Repositories\Local\Address\AddressRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAddressTest extends TestCase
{
    use RefreshDatabase;

    protected $address;

    protected function setUp(): void
    {
        parent::setUp();

        $this->address = Address::factory()->create();
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function unauthenticated_users_are_not_allowed_to_get_addresses(): void
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
     *
     * @group apiGet
     */
    public function it_can_return_an_address_by_its_id(): void
    {
        $this->signIn($this->address->user)
            ->sendRequest()
            ->assertOk()
            ->assertSee($this->address->address_line_1);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_all_required_fields(): void
    {
        $this->signIn($this->address->user)
            ->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    app()->make(AddressRepository::class)->getSelectableColumns(false),
                ],
            ]);
    }

    protected function sendRequest(): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.address.get', ['id' => $this->address->id]));
    }
}
