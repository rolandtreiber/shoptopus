<?php

namespace App\Repositories\Local\User;

interface UserRepositoryInterface {

    /**
     * get the currently authenticated user instance.
     * @param bool $returnAsArray
     * @return null|mixed
     */
    public function getCurrentUser(bool $returnAsArray = true);

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
