<?php

namespace App\Services\Local\Auth;

use App\Models\User;
use App\Events\UserSignedUp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Services\Local\User\UserServiceInterface;
use App\Services\Local\Error\ErrorServiceInterface;

class AuthService implements AuthServiceInterface
{
    private $errorService;
    private $userService;

    public function __construct(ErrorServiceInterface $errorService, UserServiceInterface $userServiceInterface) {
        $this->errorService = $errorService;
        $this->userService = $userServiceInterface;
    }

    /**
     * login api
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function login(array $payload) : array
    {
        try {
            $user = User::where('email', $payload["email"])->firstOrFail();

            if(is_null($user->password)) {
                throw new \Exception('No password set.', Config::get('api_error_codes.services.auth.mustResetPassword'));
            }

//            if (!$user->hasVerifiedEmail() && isset($payload['must_verify']) && $payload['must_verify']) {
//                $user->sendEmailVerificationNotification();
//                throw new \Exception('User unverified.', Config::get("api_error_codes.services.auth.not_verified"));
//            }

            if (!Hash::check($payload["password"], $user->password)) {
                throw new \Exception('Hash check fail', Config::get('api_error_codes.services.auth.loginUserIncorrect'));
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
     * Register api
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
     * logout api
     * @return array
     * @throws \Exception
     */
    public function logout() : array
    {
        try {
            $user = User::findOrFail(Auth::id());

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
     * Verify the user's email address.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function verify(int $id) : bool
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
     * resend the verification email
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
     * @param $user
     * @return array
     */
    private function createTokenAndGetAuthResponse($user) : array
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
            "is_verified" => $user->hasVerifiedEmail()
        ];
    }

}
