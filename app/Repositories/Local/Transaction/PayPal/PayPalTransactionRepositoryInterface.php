<?php

namespace App\Repositories\Local\Transaction\PayPal;

use PayPalHttp\HttpResponse;

interface PayPalTransactionRepositoryInterface
{
    /**
     * Store transaction
     *
     * @param  HttpResponse  $transaction
     * @param  string  $orderId
     * @return HttpResponse
     */
    public function storeTransaction(HttpResponse $transaction, string $orderId): HttpResponse;
}
