<?php

namespace App\Repositories\Local\DeliveryRule;

interface DeliveryRuleRepositoryInterface
{
    /**
     * Get the delivery types for the given delivery rules
     *
     *
     * @throws \Exception
     */
    public function getDeliveryTypes(array $deliveryTypeIds = []): array;

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
