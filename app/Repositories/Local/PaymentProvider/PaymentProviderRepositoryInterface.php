<?php

namespace App\Repositories\Local\PaymentProvider;

interface PaymentProviderRepositoryInterface
{
    /**
     * Get the configs for the given payment provider
     */
    public function getConfigs(array $paymentProviderIds = []): array;

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;

    /**
     * Get the required related models for the payment provider
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;
}
