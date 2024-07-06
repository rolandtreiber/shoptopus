<?php

namespace App\Services\Local\Order;

interface OrderServiceInterface
{
    /**
     * Get a single order
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

    /**
     * Get all orders
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

}
