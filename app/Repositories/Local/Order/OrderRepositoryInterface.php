<?php

namespace App\Repositories\Local\Order;

interface OrderRepositoryInterface
{
    /**
     * Get a single order
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

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
