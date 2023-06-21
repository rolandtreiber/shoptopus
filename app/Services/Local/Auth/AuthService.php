<?php

namespace App\Services\Local\Auth;

use App\Enums\UserInteractionType;
use App\Events\UserInteraction;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetSuccess;
use App\Repositories\Admin\User\UserRepository;
use App\Repositories\Admin\User\UserRepositoryInterface;
use App\Services\Local\Cart\CartServiceInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\Notification\NotificationServiceInterface;
use App\Services\Local\User\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    private ErrorServiceInterface $errorService;

    private UserServiceInterface $userService;

    private CartServiceInterface $cartService;

    private SocialAccountServiceInterface $socialAccountService;

    private NotificationServiceInterface $notificationService;

    private UserRepositoryInterface $userRepository;

    public function __construct(
        ErrorServiceInterface $errorService,
        UserServiceInterface $userServiceInterface,
        CartServiceInterface $cartService,
        SocialAccountServiceInterface $socialAccountServiceInterface,
        NotificationServiceInterface $notificationService,
        UserRepository $userRepository
    ) {
        $this->errorService = $errorService;
        $this->userService = $userServiceInterface;
        $this->cartService = $cartService;
        $this->socialAccountService = $socialAccountServiceInterface;
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
    }

    /**
     * Login
     *
     *
     * @throws \Exception
     */
    public function login(array $payload): array
    {
        try {
            $user = User::whereEmail($payload['email'])->first();

            if (! $user) {
                throw new \Exception('User not found.', Config::get('api_error_codes.services.auth.login_user_incorrect'));
            }

            if (is_null($user->password)) {
                // Must have signed up with a social account.
                throw new \Exception('No password set.', Config::get('api_error_codes.services.auth.must_reset_password'));
            }

            if (! Hash::check($payload['password'], $user->password)) {
                throw new \Exception('Hash check fail', Config::get('api_error_codes.services.auth.loginUserIncorrect'));
            }

            if (isset($payload['cart_id'])) {
                $this->cartService->mergeUserCarts($payload['cart_id'], $user->id);
            }

            event(new UserInteraction(UserInteractionType::Login, User::class, $user->id));

            return [
                'data' => [
                    'auth' => $this->createTokenAndGetAuthResponse($user),
                ],
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Register
     *
     *
     * @throws \Exception
     */
    public function register(array $payload): array
    {
        try {
            $user = $this->userService->getCurrentUser(false);

            if (! $user) {
                $data = [
                    'first_name' => $payload['first_name'],
                    'last_name' => $payload['last_name'],
                    'email' => $payload['email'],
                    'password' => bcrypt($payload['password']),
                    'phone' => $payload['phone'] ?? null,
                ];

                $user = $this->userService->post($data, false);

                $this->userRepository->triggerNewUserRegistrationNotification($user);
            }

            if (! $user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }

            event(new UserInteraction(UserInteractionType::Signup, User::class, $user->id));

            return [
                'data' => [
                    'message' => 'Welcome to the wonderful world of Shoptopus! Please check your email to verify',
                    'auth' => $this->createTokenAndGetAuthResponse($user),
                ],
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);

            if ($e->getCode() === Config::get('api_error_codes.services.auth.email_address_taken')) {
                throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.email_address_taken'));
            }

            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.register'));
        }
    }

    /**
     * Get the authenticated user's details
     *
     *
     * @throws \Exception
     */
    public function details(): array
    {
        try {
            $user = User::findOrFail(Auth::id());

            return [
                'data' => [
                    'auth' => [
                        'user' => $this->normalisedUserDetails($user),
                    ],
                ],
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.details'));
        }
    }

    /**
     * Verify the user's email address
     *
     *
     * @throws \Exception
     */
    public function verify(Request $request, string $id): array
    {
        try {
            $uri = 'login';
            $message = 'Email has been verified.';
            $statusCode = 200;

            if (! $request->hasValidSignature()) {
                $uri = 'verify';
                $message = 'Invalid/Expired url provided.';
                $statusCode = 401;
            } else {
                $user = User::find($id);

                if (! $user) {
                    $uri = 'verify';
                    $message = 'Sorry, there was an error while trying to verify your email address.';
                    $statusCode = 404;
                } else {
                    if (! $user->hasVerifiedEmail()) {
                        $user->markEmailAsVerified();
                    }
                    event(new UserInteraction(UserInteractionType::EmailVerified, User::class, $user->id));
                }
            }

            $url = Config::get('app.frontend_url_public')."/{$uri}?message=".urlencode($message).'&status='.urlencode($statusCode);

            return [
                'url' => $url,
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.verify'));
        }
    }

    /**
     * Resend the verification email
     *
     *
     * @throws \Exception
     */
    public function resendVerification(array $payload): array
    {
        try {
            $user = User::whereEmail($payload['email'])->firstOrFail();

            if ($user->hasVerifiedEmail()) {
                $message = 'Email has been verified.';
            } else {
                $user->sendEmailVerificationNotification();

                $message = 'Verification email re-sent.';
            }

            return ['data' => ['message' => $message]];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.resendVerification'));
        }
    }

    /**
     * Logout
     *
     *
     * @throws \Exception
     */
    public function logout(): array
    {
        try {
            $user = $this->userService->getCurrentUser(false);

            DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                ->update(['revoked' => true]);

            $user->tokens->each->revoke();

            event(new UserInteraction(UserInteractionType::Logout, User::class, $user->id));

            return ['data' => ['auth' => null]];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.logout'));
        }
    }

    /**
     * Send password reset email
     *
     *
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
                    'token' => Str::random(60),
                ]
            );

            if ($user && $passwordReset) {
                $user->sendPasswordResetNotification($passwordReset->token);
            }

            return ['data' => ['message' => 'We have e-mailed your password reset link!']];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.sendPasswordReset'));
        }
    }

    /**
     * Reset password
     *
     *
     * @throws \Exception
     */
    public function resetPassword(array $payload): array
    {
        try {
            $passwordReset = PasswordReset::where([
                ['token', $payload['token']],
                ['email', $payload['email']],
            ])->first();

            if (! $passwordReset) {
                throw new \Exception('This password reset token is invalid.');
            }

            if ($passwordReset->updated_at->addMinutes(60)->isPast()) {
                $passwordReset->delete();
                throw new \Exception('This password reset token has expired.');
            }

            $user = User::whereEmail($payload['email'])->first();

            if (! $user) {
                throw new \Exception('We cant find a user with that e-mail address.');
            }

            $user->forceFill([
                'password' => Hash::make($payload['password']),
            ])->setRememberToken(Str::random(60));

            $user->save();

            $passwordReset->delete();

            $user->notify(new PasswordResetSuccess());

            return ['data' => ['message' => 'Password successfully updated!']];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.resetPassword'));
        }
    }

    /**
     * Get the target url to the Auth provider's authentication page
     *
     *
     * @throws \Exception
     */
    public function getOAuthProviderTargetUrl(array $payload): array
    {
        try {
            return [
                'data' => [
                    'targetUrl' => $this->socialAccountService->getOAuthProviderTargetUrl($payload['provider']),
                ],
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.getOAuthProviderTargetUrl'));
        }
    }

    /**
     * Obtain the user information from the Auth provider
     *
     *
     * @throws \Exception
     */
    public function handleOAuthProviderCallback(array $payload): array
    {
        try {
            $user = User::find($this->socialAccountService->handleOAuthProviderCallback($payload)['id']);

            if (! $user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }

            return [
                'data' => [
                    'auth' => $this->createTokenAndGetAuthResponse($user),
                ],
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.handleOAuthProviderCallback'));
        }
    }

    /**
     * @throws \Exception
     */
    private function createTokenAndGetAuthResponse(User $user): array
    {
        return [
            'token' => $user->createToken(Config::get('app.name'))->accessToken,
            'token_type' => 'Bearer',
            'user' => $this->normalisedUserDetails($user),
        ];
    }

    /**
     * @throws \Exception
     */
    private function normalisedUserDetails(User $user): array
    {
        $notifications = array_map(function ($notification) {
            return [
                'id' => $notification['id'],
                'data' => json_decode($notification['data']),
            ];
        }, $this->notificationService->getAllUnreadNotificationsForUser($user->id));

        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'is_verified' => $user->hasVerifiedEmail(),
            'cart' => $this->cartService->getCartForUser($user->id),
            'notifications' => $notifications,
            'favorites' => $this->userService->getFavoritedProductIds(),
        ];
    }
}
