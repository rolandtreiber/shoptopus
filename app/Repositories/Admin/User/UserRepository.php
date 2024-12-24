<?php

namespace App\Repositories\Admin\User;

use App\Enums\AccessTokenType;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\SystemUserInviteEmail;
use App\Models\AccessToken;
use App\Models\User;
use App\Notifications\UserSignup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class UserRepository implements UserRepositoryInterface
{
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

    public function invite(string $email, string $role): bool
    {
        /** @var User $user */
        $user = auth()->user();
        $accessToken = new AccessToken();
        $accessToken->user_id = $user->id;
        $accessToken->issuer_user_id = $user->id;
        $accessToken->accessable_type = User::class;
        $accessToken->accessable_id = "NEW USER";
        $accessToken->expiry = Carbon::now()->addHours(24);
        $accessToken->type = AccessTokenType::SignupRequest;
        $accessToken->content = json_encode([
            "role" => $role,
            "email" => $email
        ]);
        $accessToken->save();
        Mail::to($email)->send(new SystemUserInviteEmail($accessToken, $user));
        return true;
    }

}
