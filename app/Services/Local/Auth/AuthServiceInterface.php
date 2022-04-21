<?php

namespace App\Services\Local\Auth;

use Illuminate\Http\Request;

interface AuthServiceInterface {

    /**
     * Login
     *
     * @param array $payload
     * @return array
     */
    public function login(array $payload) : array;

    /**
     * Register
     *
     * @param array $payload
     * @return array
     */
    public function register(array $payload) : array;

    /**
     * Resend the verification email
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function resendVerification(array $payload) : array;

    /**
     * Verify the user's email address
     *
     * @param Request $request
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function verify(Request $request, int $id) : array;

    /**
     * Logout
     *
     * @return array
     * @throws \Exception
     */
    public function logout() : array;

    /**
     * Send password reset email
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function sendPasswordReset(array $payload): array;

    /**
     * Reset password
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function resetPassword(array $payload): array;

    /**
     * Get the target url to the Auth provider's authentication page
     *
     * @param array $payload
     * @return array
     */
    public function getOAuthProviderTargetUrl(array $payload) : array;

    /**
     * Obtain the user information from the Auth provider
     *
     * @param array $payload
     * @return array
     */
    public function handleOAuthProviderCallback(array $payload) : array;

}
