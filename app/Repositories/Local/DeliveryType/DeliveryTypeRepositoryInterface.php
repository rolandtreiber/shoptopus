<?php

namespace App\Repositories\Local\DeliveryType;

interface DeliveryTypeRepositoryInterface {

    /**
     * Get the orders for the given voucher code
     *
     * @param array $deliveryTypeIds
     * @return array
     */
    public function getOrders(array $deliveryTypeIds = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
