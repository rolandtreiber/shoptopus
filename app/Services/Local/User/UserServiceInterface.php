<?php

namespace App\Services\Local\User;

interface UserServiceInterface {

    /**
     * Get the currently authenticated user instance
     *
     * @param bool $returnAsArray
     * @return mixed
     */
    public function getCurrentUser(bool $returnAsArray = true) : mixed;

    /**
     * Get the currently authenticated user's favorited product ids
     *
     * @return array
     * @throws \Exception
     */
    public function getFavoritedProductIds() : array;

}
