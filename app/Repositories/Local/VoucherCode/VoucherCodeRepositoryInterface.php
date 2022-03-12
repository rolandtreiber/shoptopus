<?php

namespace App\Repositories\Local\VoucherCode;

interface VoucherCodeRepositoryInterface {

    /**
     * Get the orders for the given voucher codes
     *
     * @param array $voucherCodeIds
     * @return array
     * @throws \Exception
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
