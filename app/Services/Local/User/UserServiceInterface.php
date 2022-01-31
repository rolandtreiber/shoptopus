<?php

namespace App\Services\Local\User;

interface UserServiceInterface {

    /**
     * Get the currently authenticated user instance
     *
     * @param bool $returnAsArray
     * @return null|mixed
     */
    public function getCurrentUser(bool $returnAsArray = true);

    /**
     * Get a user model by its email
     *
     * @param string $email
     */
    public function getByEmail(string $email) : array;
}
