<?php

namespace App\Services\Local\Cart;

interface CartServiceInterface {

    /**
     * Get the user's cart
     *
     * @param string $userId
     * @return array
     */
    public function getCartForUser(string $userId) : array;

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
     * Merge the user's carts
     *
     * @param string $userId
     * @param string $cartId
     * @return array
     */
    public function mergeUserCarts(string $userId, string $cartId) : array;

}
