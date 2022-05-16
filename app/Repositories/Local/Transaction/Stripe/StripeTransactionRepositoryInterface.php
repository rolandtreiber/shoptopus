<?php

namespace App\Repositories\Local\Transaction\Stripe;

interface StripeTransactionRepositoryInterface {

    /**
     * Store transaction
     *
     * @param array $transaction
     * @param string $orderId
     * @return array
     */
    public function storeTransaction(array $transaction, string $orderId) : array;

}
