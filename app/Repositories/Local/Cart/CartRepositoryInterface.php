<?php

namespace App\Repositories\Local\Cart;

interface CartRepositoryInterface {

    /**
     * Get the users for the carts
     *
     * @param array $userIds
     * @return array
     * @throws \Exception
     */
    public function getUsers(array $userIds = []) : array;

    /**
     * Get the products for the given carts
     *
     * @param array $cartIds
     * @return array
     */
    public function getProducts(array $cartIds = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
