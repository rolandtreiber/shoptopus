<?php

namespace App\Repositories\Local\DeliveryRule;

interface DeliveryRuleRepositoryInterface {

    /**
     * Get the deliveryTypes for the given delivery types
     *
     * @param array $deliveryRuleIds
     * @return array
     * @throws \Exception
     */
    public function getDeliveryTypes(array $deliveryRuleIds = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
