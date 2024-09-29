<?php

namespace App\Repositories\Local\Transaction\Stripe;

interface StripeTransactionRepositoryInterface
{
    /**
     * Store transaction
     */
    public function storeTransaction(array $transaction, string $orderId, string $apiKey): array;
}
