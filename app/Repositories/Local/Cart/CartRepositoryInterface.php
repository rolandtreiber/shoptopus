<?php

namespace App\Repositories\Local\Cart;

interface CartRepositoryInterface
{
    /**
     * Get the user's cart
     */
    public function getCartForUser(string $userId): array;

    /**
     * Get the users for the carts
     *
     *
     * @throws \Exception
     */
    public function getUsers(array $userIds = []): array;

    /**
     * Get the products for the given cart
     */
    public function getProducts(array $cartIds = []): array;

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
     */
    public function updateQuantity(array $payload): array;

    /**
     * Merge the user's carts
     */
    public function mergeUserCarts(string $userId, string $cartId): array;

    /**
     * Get the required related models for the given parent
     *
     *
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;
}
