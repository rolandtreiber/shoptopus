<?php

namespace App\Repositories\Local\DeliveryType;

interface DeliveryTypeRepositoryInterface
{
    /**
     * Get the delivery rules for the given delivery types
     *
     *
     * @throws \Exception
     */
    public function getDeliveryRules(array $deliveryTypeIds = []): array;

    /**
     * Get the orders for the given delivery types
     *
     *
     * @throws \Exception
     */
    public function getOrders(array $deliveryTypeIds = []): array;

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
