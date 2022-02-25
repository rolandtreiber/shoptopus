<?php

namespace App\Repositories\Local\DeliveryType;

interface DeliveryTypeRepositoryInterface {

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
