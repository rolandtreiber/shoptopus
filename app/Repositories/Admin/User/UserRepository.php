<?php

namespace App\Repositories\Admin\User;

use App\Models\User;
use App\Notifications\UserSignup;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @param User $user
     * @return bool
     */
    public function triggerNewUserRegistrationNotification(User $user): bool
    {
        $result = true;
        $notificationsConfig = config('shoptopus.notifications');
        if (array_key_exists(UserSignup::class, $notificationsConfig)) {
            try {
                $adminUsers = User::role($notificationsConfig[UserSignup::class])->get();
                foreach ($adminUsers as $adminUser) {
                    try {
                        $adminUser->notify(new UserSignup($user, $adminUser->id));
                    } catch (\Exception $exception) {
                        // Notifications should fail silently but logged
                        Log::error($exception->getMessage());
                        $result = false;
                    }
                }
            } catch (RoleDoesNotExist $exception) {
                Log::error($exception->getMessage());
            }
        }
        return $result;
    }
}
