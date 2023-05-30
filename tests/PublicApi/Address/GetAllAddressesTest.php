<?php

namespace Tests\PublicApi\Address;

use App\Models\Address;
use App\Models\User;
use App\Repositories\Local\Address\AddressRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllAddressesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function unauthenticated_users_are_not_allowed_to_get_all_their_addresses(): void
    {
        $res = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_correct_format(): void
    {
        $this->signIn()
            ->sendRequest()
            ->assertJsonStructure([
                'message',
                'data',
                'next',
                'records',
                'total_records',
            ]);
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_all_addresses_for_the_user(): void
    {
        $user = User::factory()->create();
        Address::factory()->count(5)->create(['user_id' => $user->id]);

        $res = $this->signIn($user)->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'address_line_1',
                    'address_line_2',
                    'town',
                    'post_code',
                    'country',
                    'lat',
                    'lon',
                    'deleted_at',
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
                        'is_favorite',
                    ],
                ],
            ],
        ]);

        $this->assertCount(5, $res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function soft_deleted_addresses_are_not_returned(): void
    {
        $addresses = Address::factory()->count(2)->create(['deleted_at' => now()]);

        $res = $this->signIn($addresses[0]->user)->sendRequest();

        $this->assertEmpty($res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_count(): void
    {
        $user = User::factory()->create();
        Address::factory()->count(5)->create(['user_id' => $user->id]);

        $this->assertEquals(5, $this->signIn($user)->sendRequest()->json('total_records'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_all_required_fields(): void
    {
        $address = Address::factory()->create();

        $this->signIn($address->user)
            ->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    app()->make(AddressRepository::class)->getSelectableColumns(false),
                ],
            ]);
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function addresses_can_be_filtered_by_id(): void
    {
        $user = User::factory()->create();
        $addresses = Address::factory()->count(2)->create(['user_id' => $user->id]);

        $res = $this->signIn($user)->sendRequest(['filter[id]' => $addresses[1]->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($addresses[1]->id, $res->json('data.0.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters(): void
    {
        $user = User::factory()->create();

        Address::factory()->count(3)->create(['user_id' => $user->id]);
        $address1 = Address::factory()->create(['user_id' => $user->id]);
        $address2 = Address::factory()->create(['user_id' => $user->id]);

        $res = $this->signIn($user)->sendRequest(['filter[id]' => implode(',', [$address1->id, $address2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($address1->id, $res->json('data.0.id'));
        $this->assertEquals($address2->id, $res->json('data.1.id'));
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.addresses.getAll', $data));
    }
}
