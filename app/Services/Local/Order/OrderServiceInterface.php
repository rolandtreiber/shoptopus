<?php

namespace App\Services\Local\Order;

interface OrderServiceInterface
{
    /**
     * Get a single order
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

}
