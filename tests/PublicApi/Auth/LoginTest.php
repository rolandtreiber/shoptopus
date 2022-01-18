<?php

namespace Tests\PublicApi\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        $data = [
            'email' => null,
            'password' => null
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_valid_email()
    {
        $data = [
            'email' => "lolevesgmail",
            'password' => "password"
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_correct_password()
    {
        $data = [
            'email' => $this->user->email,
            'password' => "passwordssssss"
        ];


        $res = $this->sendRequest($data)->json();

        $this->assertEquals('Incorrect email or password. Please try again.', $res['user_message']);
        $this->assertEquals('Hash check fail', $res['developer_message']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_returns_the_authenticated_user_with_a_token_upon_successful_login()
    {
        $this->artisan('passport:install');

        $data = [
            'email' => $this->user->email,
            'password' => "password"
        ];

        $this->sendRequest($data)->assertJsonStructure([
            'data' => [
                'auth' => [
                    'token',
                    'token_type',
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
                        'is_verified'
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseHas('oauth_access_tokens', ['user_id' => $this->user->id]);
    }

//    /**
//     * @test
//     * @group apiPost
//     */
//    public function users_without_a_password_are_sent_the_correct_error_response()
//    {
//        $this->user->update(['password' => null]);
//
//        $data = [
//            'email' => $this->user->email,
//            'password' => "randompassword"
//        ];
//
//        $res = $this->sendRequest($data)->json();
//
//        $this->assertEquals("Please reset your password.", $res['user_message']);
//    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.auth.login'), $data);
    }
}
