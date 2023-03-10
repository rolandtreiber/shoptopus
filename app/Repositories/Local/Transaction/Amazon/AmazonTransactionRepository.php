<?php

namespace App\Repositories\Local\Transaction\Amazon;

use App\Models\Transaction\AmazonTransaction;
use Carbon\Carbon;

class AmazonTransactionRepository implements AmazonTransactionRepositoryInterface
{
    /**
     * Store transaction
     *
     * @param  array  $transaction
     * @param  string  $orderId
     * @return array
     */
    public function storeTransaction(array $transaction, string $orderId): array
    {
        $transaction_response = json_decode($transaction['response']);

        AmazonTransaction::create([
            'order_id' => $orderId,
            'request_id' => $transaction['request_id'],
            'checkout_session_id' => $transaction_response->checkoutSessionId,
            'charge_id' => $transaction_response->chargeId,
            'product_type' => $transaction_response->productType,
            //"payment_details" => $transaction_response->paymentDetails->paymentDetails,
            //"charge_amount" => $transaction_response->paymentDetails->chargeAmount->amount,
            //"currency_code" => $transaction_response->paymentDetails->chargeAmount->currencyCode,
            'merchant_reference_id' => $transaction_response->merchantMetadata?->merchantReferenceId,
            'merchant_store_name' => $transaction_response->merchantMetadata?->merchantStoreName,
            'buyer_name' => $transaction_response->buyer?->name,
            'buyer_email' => $transaction_response->buyer?->email,
            'buyer_id' => $transaction_response->buyer?->buyerId,
            'state' => $transaction_response->statusDetails->state,
            'reason_code' => $transaction_response->statusDetails->reasonCode,
            'reason_description' => $transaction_response->statusDetails->reasonDescription,
            'amazon_last_updated_timestamp' => Carbon::parse($transaction_response->statusDetails->lastUpdatedTimestamp)->toDateTimeString(),
            //                "environment" => $transaction_response->releaseEnvironment
        ]);

        return $transaction;
    }
}
