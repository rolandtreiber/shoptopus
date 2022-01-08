<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Local\Auth\AuthServiceInterface;

class AuthController extends Controller
{
    private $modelService;

    public function __construct(AuthServiceInterface $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * login api
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->modelService->login($request->validated()));
        } catch (\Exception $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        } catch (\Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    public function login(LoginRequest $request)
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
}
