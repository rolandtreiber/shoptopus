<?php

namespace App\Repositories\Local\Address;

interface AddressRepositoryInterface
{
    /**
     * Get the users for the given addresses
     *
     * @param  array  $userIds
     * @return array
     *
     * @throws \Exception
     */
    public function getUsers(array $userIds = []): array;

    /**
     * Get the required related models for the given parent
     *
     * @param $result
     * @param  array  $excludeRelationships
     * @return array
     *
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;

    /**
     * Get the columns for selection
     *
     * @param  bool  $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;
}
