<?php

namespace Tests\PublicApi\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUserDetailsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group apiGet
     */
    public function unauthenticated_users_cannot_access_their_details()
    {
        $res = $this->sendRequest();

        $this->assertEquals('Unauthenticated.', $res->json('developer_message'));
        $this->assertEquals(1101, $res->json('error_code'));
    }

    /**
     * @test
     * @group apiGet
     */
    public function authenticated_users_can_get_their_details_containing_all_required_fields()
    {
        $res = $this->signIn()->sendRequest();

        $res->assertSuccessful();

        $res->assertJsonStructure([
            'data' => [
                'auth' => [
                    'user' => [
                        'id',
                        'name',
                        'first_name',
                        'last_name',
                        'email',
                        'phone',
                        'avatar' => [
                            'url',
                            'file_name'
                        ],
                        'is_verified',
                        'cart' => [
                            'id',
                            'user_id',
                            'ip_address',
                            'user',
                            'products'
                        ]
                    ]
                ]
            ]
        ]);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.auth.details'));
    }
}
