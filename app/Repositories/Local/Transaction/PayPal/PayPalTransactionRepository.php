<?php

namespace App\Repositories\Local\Transaction\PayPal;

use App\Models\Transaction\PayPalTransaction;
use PayPalHttp\HttpResponse;

/**
 * @mixin PayPalTransaction
 */
class PayPalTransactionRepository implements PayPalTransactionRepositoryInterface
{
    /**
     * Store transaction
     */
    public function storeTransaction(HttpResponse $transaction, string $orderId): HttpResponse
    {
        /** @var object{'id': string, 'intent': string, 'status': string, 'purchase_units': array, 'payer': object} $result */
        $result = $transaction->result;

        PayPalTransaction::create([
            'order_id' => $orderId,
            'status_code' => $transaction->statusCode,
            'transaction_id' => $result->id,
            'intent' => $result->intent,
            'status' => $result->status,
            'reference_id' => $result->purchase_units[0]->reference_id,
            'charge_amount' => $result->purchase_units[0]->amount->value,
            'currency_code' => $result->purchase_units[0]->amount->currency_code,
            'merchant_id' => $result->purchase_units[0]->payee->merchant_id,
            'merchant_email' => $result->purchase_units[0]->payee->email_address,
            'soft_descriptor' => $result->purchase_units[0]->soft_descriptor ?? null,
            'payer_firstname' => $result->payer->name->given_name,
            'payer_surname' => $result->payer->name->surname,
            'payer_email' => $result->payer->email_address,
            'payer_id' => $result->payer->payer_id,
        ]);

        return $transaction;
    }
}
