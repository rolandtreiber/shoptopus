<?php

namespace App\Repositories\Local\Cart;

interface CartRepositoryInterface {

    /**
     * Get the user's cart
     *
     * @param string $userId
     * @return array
     */
    public function getCartForUser(string $userId) : array;

    /**
     * Get the users for the carts
     *
     * @param array $userIds
     * @return array
     * @throws \Exception
     */
    public function getUsers(array $userIds = []) : array;

    /**
     * Get the products for the given cart
     *
     * @param array $cartIds
     * @return array
     */
    public function getProducts(array $cartIds = []) : array;

    /**
     * Add item to cart.
     *
     * @param array $payload
     * @return array
     */
    public function addItem(array $payload) : array;

    /**
     * Remove item from cart.
     *
     * @param array $payload
     * @return array
     */
    public function removeItem(array $payload) : array;

    /**
     * Update quantity for a given product
     *
     * @param array $payload
     * @return array
     */
    public function updateQuantity(array $payload) : array;

    /**
     * Merge the user's carts
     *
     * @param string $userId
     * @param string $cartId
     * @return array
     */
    public function mergeUserCarts(string $userId, string $cartId) : array;

    /**
     * Get the required related models for the given parent
     *
     * @param $result
     * @param array $excludeRelationships
     * @return array
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
