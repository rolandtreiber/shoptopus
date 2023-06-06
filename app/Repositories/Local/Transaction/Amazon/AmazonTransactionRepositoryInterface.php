<?php

namespace App\Repositories\Local\Transaction\Amazon;

interface AmazonTransactionRepositoryInterface
{
    /**
     * Store transaction
     */
    public function storeTransaction(array $transaction, string $orderId): array;
}
