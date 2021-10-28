<?php

namespace App\Http\Controllers;

use App\Enums\AuthFlows;
use App\Events\Admin\UserPasswordReset;
use App\Http\Requests\Auth\EmailConfirmationRequest;
use App\Http\Resources\Admin\UserDetailResource;
use App\Models\AccessToken;
use App\Enums\AccessTokenTypes;
use App\Enums\TokenCheckOutcomeTypes;
use App\Events\Admin\UserSignup;
use App\Exceptions\ApiValidationFailedException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\UpdatePasswordFromResetFlowRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @throws Exception
     */
    public function apiLoginAttempt(LoginRequest $request)
    {
        new Client;

        $authRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('passport.grant_id'),
            'client_secret' => config('passport.secret'),
            'username' => $request->email,
            'password' => $request->password
        ], [], [], []);

        $res = app()->handle($authRequest);
        if ($res->getStatusCode() !== 200) {
            // Guzzle did not authenticate so let's return Guzzle's non-200 error response
            return $res;
        }

        $content = json_decode($res->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (array_key_exists('user', $content)) {
            if ($content['user']['email_confirmed'] === false) {
                return [
                    'status' => 'error',
                    'message' => 'The email is not confirmed.'
                ];
            }
        }

        $responseData = json_decode($res->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return response()->json($responseData);

    }

    /**
     * @param SignupRequest $request
     * @return string[]
     * @throws ApiValidationFailedException
     */
    public function apiSignup(SignupRequest $request): array
    {
        $taken = User::where('email', $request->email)->first();
        if ($taken) {
            return [
                'status' => 'error',
                'message' => 'The email is already registered.'
            ];
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        event(new UserSignup($user));
        return [
            'status' => 'success',
            'message' => 'Signup successful. Please check your email.'
        ];
    }

    /**
     * @param EmailConfirmationRequest $request
     * @return string[]
     */
    public function confirmEmail(EmailConfirmationRequest $request): array
    {
        $token = $request->email_confirmation_token;
        $accessToken = AccessToken::where('token', '=', $token)->where('type', AccessTokenTypes::EmailConfirmation)->first();
        $now = Carbon::now();
        if (!$accessToken) {
            return [
                'status' => 'error',
                'message' => 'Invalid token'
            ];
        }

        $expiry = Carbon::parse($accessToken->expiry);
        if ($expiry < $now) {
            return [
                'status' => 'error',
                'message' => 'Token expired'
            ];
        }
        $user = (new User)->find($accessToken->user_id);
        if ($user) {
            $user->email_verified_at = Carbon::now();
            $user->save();
        } else {
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }
        return [
            'status' => 'success',
            'message' => 'Email confirmed'
        ];
    }

    /**
     * @param $token
     * @return Application|Factory|View
     */
    public function checkPasswordResetToken($token)
    {
        $accessToken = AccessToken::where('token', '=', $token)->where('type', AccessTokenTypes::PasswordReset)->first();
        $now = Carbon::now();
        if (!$accessToken) {
            return view('auth.PasswordResetForm', ['user' => null, 'type' => TokenCheckOutcomeTypes::TokenInvalid]);
        }

        $expiry = Carbon::parse($accessToken->expiry);
        if ($expiry < $now) {
            return view('auth.PasswordResetForm', ['user' => $accessToken->user, 'type' => TokenCheckOutcomeTypes::TokenExpired]);
        }
        return view('auth.PasswordResetForm', ['user' => $accessToken->user, 'type' => TokenCheckOutcomeTypes::Success]);
    }

    public function resetPassword(PasswordResetRequest $request): array
    {
        $user = User::where('email', '=', $request->email)->first();
        if ($user) {
            $request->flow === AuthFlows::Admin && event(new UserPasswordReset($user));
        }
        return [
            'status' => 'success',
            'message' => 'If there is an account associated with this email, we have sent the recovery email to it.'
        ];
    }

    /**
     * @param UpdatePasswordFromResetFlowRequest $request
     * @return string[]
     */
    public function updatePasswordFromResetFlow(UpdatePasswordFromResetFlowRequest $request): array
    {
        $accessToken = AccessToken::where('token', $request->password_reset_token)->first();
        if (!$accessToken) {
            return [
                'status' => 'error',
                'message' => 'The token is invalid.'
            ];
        }

        if (!$accessToken->checkExpiry()) {
            return [
                'status' => 'error',
                'message' => 'The token has expired.',
            ];
        }
        $user = User::where('id', $accessToken->user_id)->first();
        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'User not found.',
            ];
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $accessToken->delete();
        return [
            'status' => 'success',
            'message' => 'Your password was reset successfully. You can now log in.',
        ];
    }

    /**
     * @return UserDetailResource
     */
    public function getAuthenticatedUser(): UserDetailResource
    {
        return new UserDetailResource(Auth()->user());
    }

}
