<?php

namespace App\Services\Local\Auth;

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
     * @param string $id
     * @return bool
     * @throws \Exception
     */
    public function verify(string $id) : bool;

    /**
     * Logout
     *
     * @return array
     * @throws \Exception
     */
    public function logout() : array;

}
