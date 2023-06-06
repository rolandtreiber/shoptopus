<?php

namespace App\Repositories\Local\AccessToken;

interface AccessTokenRepositoryInterface
{
    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;
}
