<?php

namespace App\Services\Local\Auth;

interface AuthServiceInterface {

    /**
     * Login api
     * @param array $payload
     * @return array
     */
    public function login(array $payload) : array;

    /**
     * Register api
     * @param array $payload
     * @return array
     */
    public function register(array $payload) : array;

}
