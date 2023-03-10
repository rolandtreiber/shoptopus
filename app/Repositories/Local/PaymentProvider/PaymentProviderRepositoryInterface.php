<?php

namespace App\Repositories\Local\PaymentProvider;

interface PaymentProviderRepositoryInterface
{
    /**
     * Get the configs for the given payment provider
     *
     * @param  array  $paymentProviderIds
     * @return array
     */
    public function getConfigs(array $paymentProviderIds = []): array;

    /**
     * Get the columns for selection
     *
     * @param  bool  $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;

    /**
     * Get the required related models for the payment provider
     *
     * @param $result
     * @param  array  $excludeRelationships
     * @return array
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;
}
