<?php

namespace App\Repositories\Local\VoucherCode;

interface VoucherCodeRepositoryInterface
{
    /**
     * Get the orders for the given voucher codes
     *
     *
     * @throws \Exception
     */
    public function getOrders(array $voucherCodeIds = []): array;

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
