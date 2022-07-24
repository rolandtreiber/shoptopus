<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ProductOutOfStock;
use App\Notifications\ProductRunningLow;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @group notifications
 */
class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected array $users = [];
    protected User $customer;
    protected array $notificationsConfig;

    public function setUp() : void
    {
        parent::setUp();
        Mail::fake();
        $this->seed(RoleSeeder::class);
        $this->notificationsConfig = config('shoptopus.notifications');
        /** @var User $customer */
        $this->customer = User::factory()->create()->assignRole(UserRole::Customer);

    }

    private function createRequiredUsers($roles) {
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
            'stock' => 4
        ])->create();
        $totalNotificationsShouldBeSent = count($this->notificationsConfig[ProductRunningLow::class]);
        $this->assertDatabaseCount('notifications', $totalNotificationsShouldBeSent);
        foreach ($this->users as $user) {
            $this->assertDatabaseHas('notifications', [
                'type' => ProductRunningLow::class,
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
            ]);
        }
        $this->assertDatabaseMissing('notifications', [
            'type' => ProductRunningLow::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $this->customer->id,
        ]);
    }

    /**
     * @test
     */
    public function test_product_out_of_stock_notification_is_sent()
    {
        $this->createRequiredUsers($this->notificationsConfig[ProductOutOfStock::class]);
        Product::factory()->state([
            'stock' => 0
        ])->create();
        $totalNotificationsShouldBeSent = count($this->notificationsConfig[ProductOutOfStock::class]);
        $this->assertDatabaseCount('notifications', $totalNotificationsShouldBeSent);
        foreach ($this->users as $user) {
            $this->assertDatabaseHas('notifications', [
                'type' => ProductOutOfStock::class,
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
            ]);
        }
        $this->assertDatabaseMissing('notifications', [
            'type' => ProductOutOfStock::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $this->customer->id,
        ]);
    }

}
