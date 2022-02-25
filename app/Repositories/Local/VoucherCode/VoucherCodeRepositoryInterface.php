<?php

namespace App\Repositories\Local\VoucherCode;

interface VoucherCodeRepositoryInterface {

    /**
     * Get the orders for the given voucher code
     *
     * @param array $voucherCodeIds
     * @return array
     */
    public function getOrders(array $voucherCodeIds = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
