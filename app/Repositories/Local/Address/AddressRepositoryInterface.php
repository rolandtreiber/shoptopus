<?php

namespace App\Repositories\Local\Address;

interface AddressRepositoryInterface
{
    /**
     * Get the users for the given addresses
     *
     *
     * @throws \Exception
     */
    public function getUsers(array $userIds = []): array;

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
