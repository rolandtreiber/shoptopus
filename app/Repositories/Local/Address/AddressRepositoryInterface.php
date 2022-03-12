<?php

namespace App\Repositories\Local\Address;

interface AddressRepositoryInterface {

    /**
     * Get the users for the given addresses
     *
     * @param array $userIds
     * @return array
     * @throws \Exception
     */
    public function getUsers(array $userIds = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
