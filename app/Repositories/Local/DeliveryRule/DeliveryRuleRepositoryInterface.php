<?php

namespace App\Repositories\Local\DeliveryRule;

interface DeliveryRuleRepositoryInterface {

    /**
     * Get the delivery types for the given delivery rules
     *
     * @param array $deliveryTypeIds
     * @return array
     * @throws \Exception
     */
    public function getDeliveryTypes(array $deliveryTypeIds = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
