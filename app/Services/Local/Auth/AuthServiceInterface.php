<?php

namespace App\Services\Local\Auth;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

interface AuthServiceInterface
{
    /**
     * Login
     */
    public function login(array $payload): array;

    /**
     * Register
     */
    public function register(array $payload, Role $role): array;

    /**
     * Resend the verification email
     *
     *
     * @throws \Exception
     */
    public function resendVerification(array $payload): array;

    /**
     * Verify the user's email address
     *
     *
     * @throws \Exception
     */
    public function verify(Request $request, string $id): array;

    /**
     * Logout
     *
     *
     * @throws \Exception
     */
    public function logout(): array;

    /**
     * Send password reset email
     *
     *
     * @throws \Exception
     */
    public function sendPasswordReset(array $payload): array;

    /**
     * Reset password
     *
     *
     * @throws \Exception
     */
    public function resetPassword(array $payload): array;

    /**
     * Get the target url to the Auth provider's authentication page
     */
    public function getOAuthProviderTargetUrl(array $payload): array;

    /**
     * Obtain the user information from the Auth provider
     */
    public function handleOAuthProviderCallback(array $payload): array;

    public function details(): array;

    public function flushRolesAndPermissions(): void;
}
