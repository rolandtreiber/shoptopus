<?php

namespace App\Repositories\Local\VoucherCode;

interface VoucherCodeRepositoryInterface {

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
