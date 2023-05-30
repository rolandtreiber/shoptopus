<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrder;
use App\Notifications\ProductOutOfStock;
use App\Notifications\ProductRunningLow;
use App\Notifications\UserSignup;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * @group notifications
 */
class AdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected array $users = [];

    protected User $customer;

    protected array $notificationsConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->notificationsConfig = config('shoptopus.notifications');
        /** @var User $customer */
        $this->customer = User::factory()->create()->assignRole(UserRole::Customer);
    }

    private function createRequiredUsers($roles)
    {
        $users = [];
        foreach ($roles as $role) {
            $users[] = User::factory()->create()->assignRole($role);
        }

        $this->users = $users;
    }

    /**
     * @test
     */
    public function test_product_running_low_notification_is_sent()
    {
        $this->createRequiredUsers($this->notificationsConfig[ProductRunningLow::class]);
        Product::factory()->state([
            'stock' => 4,
        ])->create();
        foreach ($this->users as $user) {
            Notification::assertSentTo($user, ProductRunningLow::class);
        }
        Notification::assertNotSentTo($this->customer, ProductRunningLow::class);
    }

    /**
     * @test
     */
    public function test_product_out_of_stock_notification_is_sent()
    {
        $this->createRequiredUsers($this->notificationsConfig[ProductOutOfStock::class]);
        Product::factory()->state([
            'stock' => 0,
        ])->create();
        foreach ($this->users as $user) {
            Notification::assertSentTo($user, ProductOutOfStock::class);
        }
        Notification::assertNotSentTo($this->customer, ProductOutOfStock::class);
    }

    /**
     * @test
     */
    public function test_new_order_notification_is_sent()
    {
        $this->createRequiredUsers($this->notificationsConfig[NewOrder::class]);
        Order::factory()->create();
        foreach ($this->users as $user) {
            Notification::assertSentTo($user, NewOrder::class);
        }
        Notification::assertNotSentTo($this->customer, NewOrder::class);
    }

    /**
     * @test
     *
     * @group apiPost
     *
     * @see https://github.com/laravel/framework/issues/19952
     */
    public function test_user_signup_notification_is_sent()
    {
        $this->createRequiredUsers($this->notificationsConfig[UserSignup::class]);

        $this->artisan('passport:install');

        $data = [
            'first_name' => 'Istvan',
            'last_name' => 'Lovas',
            'email' => 'loleves@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->postJson(route('api.auth.register'), $data);
        foreach ($this->users as $user) {
            Notification::assertSentTo($user, UserSignup::class);
        }
        Notification::assertNotSentTo($this->customer, UserSignup::class);
    }
}
