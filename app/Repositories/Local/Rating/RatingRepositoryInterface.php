<?php

namespace App\Repositories\Local\Rating;

use App\Models\Product;

interface RatingRepositoryInterface
{
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
