<?php

namespace App\Services\Local\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Events\UserSignedUp;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Notifications\PasswordResetSuccess;
use App\Services\Local\Cart\CartServiceInterface;
use App\Services\Local\User\UserServiceInterface;
use App\Services\Local\Error\ErrorServiceInterface;

class AuthService implements AuthServiceInterface
{
    private ErrorServiceInterface $errorService;
    private UserServiceInterface $userService;
    private CartServiceInterface $cartService;

    public function __construct(
        ErrorServiceInterface $errorService,
        UserServiceInterface $userServiceInterface,
        CartServiceInterface $cartService
    ) {
        $this->errorService = $errorService;
        $this->userService = $userServiceInterface;
        $this->cartService = $cartService;
    }

    /**
     * Login
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function login(array $payload) : array
    {
        try {
            $user = User::whereEmail($payload['email'])->first();

            if (!$user) {
                throw new \Exception('User not found.', Config::get('api_error_codes.services.auth.login_user_incorrect'));
            }

            if (!Hash::check($payload["password"], $user->password)) {
                throw new \Exception('Hash check fail', Config::get('api_error_codes.services.auth.loginUserIncorrect'));
            }

            if (isset($payload['cart_id'])) {
                $this->cartService->mergeUserCarts($payload['cart_id'], $user->id);
            }

            return [
                "data" => [
                    "auth" => $this->createTokenAndGetAuthResponse($user)
                ]
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        } catch (\Error $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Register
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function register(array $payload) : array
    {
        try {
            $user = $this->userService->getCurrentUser(false);

            if(!$user) {
                $data = [
                    "first_name" => $payload['first_name'],
                    "last_name" => $payload['last_name'],
                    "email" => $payload['email'],
                    "password" => bcrypt($payload['password']),
                    "phone" => $payload['phone'] ?? null
                ];

                $user = $this->userService->post($data, false);

                UserSignedUp::dispatch($user);
            }

            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }

            return [
                "data" => [
                    "message" => "Welcome to the wonderful world of Shoptopus! Please check your email to verify",
                    "auth" => $this->createTokenAndGetAuthResponse($user)
                ]
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.register'));
        } catch (\Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.register'));
        }
    }

    /**
     * Resend the verification email
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function resendVerification(array $payload) : array
    {
        try {
            $user = User::whereEmail($payload['email'])->firstOrFail();

            if ($user->hasVerifiedEmail()) {
                $message = "Email has been verified.";
            } else {
                $user->sendEmailVerificationNotification();

                $message = "Verification email re-sent.";
            }

            return ["data" => ["message" => $message]];
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.resendVerification'));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.resendVerification'));
        }
    }

    /**
     * Verify the user's email address.
     *
     * @param string $id
     * @return bool
     * @throws \Exception
     */
    public function verify(string $id) : bool
    {
        try {
            $user = User::findOrFail($id);

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return true;
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.verify'));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.verify'));
        }
    }

    /**
     * Logout
     *
     * @return array
     * @throws \Exception
     */
    public function logout() : array
    {
        try {
            $user = $this->userService->getCurrentUser(false);

            DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                ->update(['revoked' => true]);

            $user->tokens->each->revoke();

            return ["data" => ["auth" => null]];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.logout'));
        } catch (\Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.logout'));
        }
    }

    /**
     * Send password reset email
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function sendPasswordReset(array $payload): array
    {
        try {
            $user = User::whereEmail($payload['email'])->firstOrFail();

            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => Str::random(60)
                ]
            );

            if ($user && $passwordReset) {
                $user->sendPasswordResetNotification($passwordReset->token);
            }

            return ["data" => ["message" => "We have e-mailed your password reset link!"]];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.sendPasswordReset'));
        }
    }

    /**
     * Reset password
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function resetPassword(array $payload): array
    {
        try {
            $passwordReset = PasswordReset::where([
                ['token', $payload['token']],
                ['email', $payload['email']]
            ])->first();

            if (!$passwordReset) {
                throw new \Exception("This password reset token is invalid.");
            }

            if ($passwordReset->updated_at->addMinutes(60)->isPast()) {
                $passwordReset->delete();
                throw new \Exception("This password reset token has expired.");
            }

            $user = User::whereEmail($payload['email'])->first();

            if (!$user) {
                throw new \Exception("We cant find a user with that e-mail address.");
            }

            $user->forceFill([
                'password' => Hash::make($payload['password'])
            ])->setRememberToken(Str::random(60));

            $user->save();

            $passwordReset->delete();

            $user->notify(new PasswordResetSuccess());

            return ["data" => ["message" => "Password successfully updated!"]];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.resetPassword'));
        }
    }

    /**
     * @param User $user
     * @return array
     */
    private function createTokenAndGetAuthResponse(User $user) : array
    {
        return [
            'token' => $user->createToken(Config::get('app.name'))->accessToken,
            'token_type' => 'Bearer',
            'user' => $this->normalisedUserDetails($user)
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function normalisedUserDetails(User $user) : array
    {
        return [
            "id" => $user->id,
            "name" => $user->name,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "phone" => $user->phone,
            "avatar" => $user->avatar,
            "is_verified" => $user->hasVerifiedEmail(),
            "cart" => $this->cartService->getCartForUser($user->id)
        ];
    }

}
