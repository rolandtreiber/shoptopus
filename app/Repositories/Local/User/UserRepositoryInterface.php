<?php

namespace App\Repositories\Local\User;

interface UserRepositoryInterface {

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


    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
