<?php

namespace App\Repositories\Local\Transaction\PayPal;

use PayPalHttp\HttpResponse;

interface PayPalTransactionRepositoryInterface
{
    /**
     * Store transaction
     */
    public function storeTransaction(HttpResponse $transaction, string $orderId): HttpResponse;
}
