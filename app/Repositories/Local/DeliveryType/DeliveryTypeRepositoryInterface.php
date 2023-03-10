<?php

namespace App\Repositories\Local\DeliveryType;

interface DeliveryTypeRepositoryInterface
{
    /**
     * Get the delivery rules for the given delivery types
     *
     * @param  array  $deliveryTypeIds
     * @return array
     *
     * @throws \Exception
     */
    public function getDeliveryRules(array $deliveryTypeIds = []): array;

    /**
     * Get the orders for the given delivery types
     *
     * @param  array  $deliveryTypeIds
     * @return array
     *
     * @throws \Exception
     */
    public function getOrders(array $deliveryTypeIds = []): array;

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
