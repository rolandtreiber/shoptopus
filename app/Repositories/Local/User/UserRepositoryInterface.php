<?php

namespace App\Repositories\Local\User;

interface UserRepositoryInterface
{
    /**
     * Get the currently authenticated user instance
     */
    public function getCurrentUser(bool $returnAsArray = true): mixed;

    /**
     * Get the currently authenticated user's favorited products
     *
     *
     * @throws \Exception
     */
    public function favorites(): array;

    /**
     * Get the currently authenticated user's favorited product ids
     *
     *
     * @throws \Exception
     */
    public function getFavoritedProductIds(): array;

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;
}
