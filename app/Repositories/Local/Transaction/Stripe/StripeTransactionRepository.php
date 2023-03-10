<?php

namespace App\Repositories\Local\Transaction\Stripe;

use App\Models\Transaction\StripeTransaction;
use Illuminate\Support\Carbon;

class StripeTransactionRepository implements StripeTransactionRepositoryInterface
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
        StripeTransaction::create([
            'order_id' => $orderId,
            'payment_id' => $transaction['id'],
            'object' => $transaction['object'],
            'amount' => $transaction['amount'],
            'canceled_at' => is_null($transaction['canceled_at']) ? null : Carbon::createFromTimestamp($transaction['canceled_at'])->toDateTimeString(),
            'cancellation_reason' => $transaction['cancellation_reason'] ?? null,
            'capture_method' => $transaction['capture_method'],
            'confirmation_method' => $transaction['confirmation_method'],
            'created' => is_null($transaction['created']) ? null : Carbon::createFromTimestamp($transaction['created'])->toDateTimeString(),
            'currency' => $transaction['currency'],
            'description' => $transaction['description'] ?? null,
            'last_payment_error' => $transaction['last_payment_error'] ?? null,
            'livemode' => $transaction['livemode'],
            'next_action' => $transaction['next_action'] ?? null,
            'next_source_action' => $transaction['next_source_action'] ?? null,
            'payment_method' => $transaction['payment_method'] ?? null,
            'payment_method_types' => implode(', ', $transaction['payment_method_types']),
            'receipt_email' => $transaction['receipt_email'] ?? null,
            'setup_future_usage' => $transaction['setup_future_usage'] ?? null,
            'shipping' => $transaction['shipping'] ?? null,
            'source' => $transaction['source'] ?? null,
            'status' => $transaction['status'],
        ]);

        return $transaction;
    }
}
