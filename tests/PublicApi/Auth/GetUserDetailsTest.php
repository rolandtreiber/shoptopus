<?php

namespace Tests\PublicApi\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
                        ],
                        'notifications'
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function the_notifications_array_contains_all_the_unread_notifications()
    {
        $user = User::factory()->create();

        $notificationData = [
            'type' => "competition-entered",
            'title' => "You have entered a Competition.",
            'level' => 'info'
        ];

        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\YouEnteredACompetition',
            'notifiable_type' => 'App\Models\User\User',
            'notifiable_id' =>$user->id,
            'data' => json_encode($notificationData)
        ]);

        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\YouEnteredACompetition',
            'notifiable_type' => 'App\Models\User\User',
            'notifiable_id' =>$user->id,
            'data' => json_encode($notificationData),
            'read_at' => now()
        ]);

        $notifications = $this->signIn($user)->sendRequest()->json('data.auth.user.notifications');

        $this->assertCount(1, $notifications);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.auth.details'));
    }
}
