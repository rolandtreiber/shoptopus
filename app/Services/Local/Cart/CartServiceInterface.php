<?php

namespace App\Services\Local\Cart;

interface CartServiceInterface
{
    /**
     * Get the user's cart
     */
    public function getCartForUser(string $userId): array;

    /**
     * Add item to cart.
     */
    public function addItem(array $payload): array;

    /**
     * Remove item from cart.
     */
    public function removeItem(array $payload): array;

    /**
     * Update quantity for a given product
     *
     *
     * @throws \Exception
     */
    public function updateQuantity(array $payload): array;

    /**
     * Merge the user's carts
     */
    public function mergeUserCarts(string $userId, string $cartId): array;

    public function update(string $id, array $payload);
}
