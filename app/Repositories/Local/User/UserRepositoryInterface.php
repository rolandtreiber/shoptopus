<?php

namespace App\Repositories\Local\User;

interface UserRepositoryInterface {

    /**
     * get currently authenticated user details
     * @return null|array
     */
    public function getCurrentUser() : ?array;

    /**
     * get a user model by its email
     * @param string $email
     */
    public function getByEmail(string $email) : array;


    /**
     * get the columns for selection
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
