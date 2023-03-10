<?php

namespace Tests\PublicApi\Auth;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

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
                            'file_name',
                        ],
                        'is_verified',
                        'cart' => [
                            'id',
                            'user_id',
                            'ip_address',
                            'user',
                            'products',
                        ],
                        'notifications',
                        'favorites',
                    ],
                ],
            ],
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
            'type' => 'order-complete',
            'title' => "You're order is ready.",
            'level' => 'info',
        ];

        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\OrderComplete',
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode($notificationData),
        ]);

        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\OrderComplete',
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode($notificationData),
            'read_at' => now(),
        ]);

        $notifications = $this->signIn($user)->sendRequest()->json('data.auth.user.notifications');

        $this->assertCount(1, $notifications);
    }

    /**
     * @test
     * @group apiGet
     */
    public function the_favorites_array_contains_all_the_favorited_product_ids()
    {
        $user = User::factory()->create();

        $products = Product::factory()->count(3)->create();

        $products->each(fn ($product) => DB::table('favorited_products')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]));

        $favorites = $this->signIn($user)->sendRequest()->json('data.auth.user.favorites');

        $this->assertCount($products->count(), $favorites);
    }

    protected function sendRequest(): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.auth.details'));
    }
}
