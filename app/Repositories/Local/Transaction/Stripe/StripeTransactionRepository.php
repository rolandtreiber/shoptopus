<?php

namespace App\Repositories\Local\Transaction\Stripe;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Exceptions\CheckoutException;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSource;
use App\Models\Transaction\StripeTransaction;
use Illuminate\Support\Carbon;
use Stripe\StripeClient;

class StripeTransactionRepository implements StripeTransactionRepositoryInterface
{
    /**
     * Store transaction
     * @throws CheckoutException
     */
    public function storeTransaction(array $transaction, string $orderId, string $apiKey): array
    {
        if (config('app.env') !== 'testing') {
            $stripe = new StripeClient($apiKey);
            $intent = $stripe->paymentIntents->retrieve($transaction['payment_intent_id']);
            try {
               if ($intent->charges->total_count > 0) {
                    $transaction = $intent->toArray()['charges']['data'][0];
               }
               $intent = $intent->toArray();
            } catch(\Exception $exception) {
                throw new CheckoutException('Payment could not be saved. Intent: '.$transaction['payment_intent_id']. ". Message: ".$exception->getMessage());
            }
        } else {
            $intent = $transaction;
        }

        StripeTransaction::create([
            'order_id' => $orderId,
            'payment_id' => array_key_exists('id', $transaction) ? $transaction['id'] : null,
            'object' => array_key_exists("object", $intent) ? $intent['object'] : null,
            'amount' => array_key_exists("amount", $intent) ? $intent['amount'] : null,
            'canceled_at' => array_key_exists("canceled_at", $intent) ? Carbon::createFromTimestamp($intent['canceled_at'])->toDateTimeString() : null,
            'cancellation_reason' => array_key_exists("cancellation_reason", $intent) ? $intent['cancellation_reason'] : null,
            'capture_method' => array_key_exists('capture_method', $intent) ? $intent['capture_method'] : "",
            'confirmation_method' => array_key_exists('confirmation_method', $intent) ? $intent['confirmation_method'] : "",
            'created' => array_key_exists('created', $transaction) ? Carbon::createFromTimestamp($transaction['created'])->toDateTimeString() : null,
            'currency' => array_key_exists('currency', $transaction) ? $transaction['currency'] : "unknown",
            'description' => array_key_exists('description', $transaction) ? $transaction['description'] : null,
            'last_payment_error' => array_key_exists('last_payment_error', $intent) ? $intent['last_payment_error'] : null,
            'livemode' => array_key_exists('livemode', $intent) ? $intent['livemode'] : "",
            'next_action' => array_key_exists('next_action', $intent) ? $intent['next_action'] : null,
            'next_source_action' => array_key_exists('next_source_action', $intent) ? $intent['next_source_action'] : null,
            'payment_method' => array_key_exists('payment_method', $intent) ? $intent['payment_method'] : null,
            'payment_method_types' => array_key_exists('payment_method_types', $intent) ? implode(', ', $intent['payment_method_types']) : "",
            'receipt_email' => array_key_exists('receipt_email', $intent) ? $intent['receipt_email'] : null,
            'setup_future_usage' => array_key_exists('setup_future_usage', $intent) ? $intent['setup_future_usage'] : null,
            'shipping' => array_key_exists('shipping', $intent) ? $intent['shipping'] : null,
            'source' => array_key_exists('source', $intent) ? $intent['source'] : null,
            'status' => array_key_exists('status', $intent) ? $intent['status'] : null,
        ]);

        if (config('app.env') !== 'testing') {

            if (array_key_exists('payment_method', $transaction) && array_key_exists('payment_method_details', $transaction)) {
                $paymentSource = PaymentSource::where(['source_id' => $transaction['payment_method']])->first();
                if (!$paymentSource) {
                    $paymentSource = new PaymentSource();
                }
                $paymentSource->user_id = Auth()->user()->id;
                $paymentSourceDetails = $transaction['payment_method_details'];
                if (array_key_exists('card', $paymentSourceDetails)) {
                    $cardDetails = $paymentSourceDetails['card'];
                }
                $brand = array_key_exists('brand', $cardDetails) ? $cardDetails['brand'] : "UNKNOWN BRAND";
                $expMonth = array_key_exists('exp_month', $cardDetails) ? $cardDetails['exp_month'] : "UNKNOWN EXP MONTH";
                $expYear = array_key_exists('exp_year', $cardDetails) ? $cardDetails['exp_year'] : "UNKNOWN EXP YEAR";
                $lastFour = array_key_exists('last4', $cardDetails) ? $cardDetails['last4'] : "UNKNOWN LAST 4 DIGITS";
                $paymentSource->name = Auth()->user()->name . '-' . $brand;
                $paymentSource->stripe_user_id = '';
                $paymentSource->source_id = $transaction['payment_method'];
                $paymentSource->payment_method_id = PaymentMethod::Stripe;
                $paymentSource->brand = $brand;
                $paymentSource->exp_month = $expMonth;
                $paymentSource->exp_year = $expYear;
                $paymentSource->last_four = $lastFour;
                $paymentSource->save();
            }

            $payment = new Payment();
            $payment->payable_type = Order::class;
            $payment->payable_id = $orderId;
            $payment->amount = array_key_exists("amount", $intent) ? round($intent['amount'] / 100, 2) : null;
            $payment->user_id = Auth()->user()->id;
            $payment->payment_source_id = $paymentSource->id;
            $payment->proof = [
                'processed_at' => Carbon::now()->format("Y-m-d H:i:s"),
                'status' => array_key_exists('status', $transaction) ? $transaction['status'] : "UNKNOWN"
            ];
            $payment->status = PaymentStatus::Settled;
            $payment->payment_ref = array_key_exists('payment_method', $transaction) ? $transaction['payment_method'] : "UNKNOWN";
            $payment->method_ref = array_key_exists('payment_method', $transaction) ? $transaction['payment_method'] : "UNKNOWN";
            $payment->type = PaymentType::Payment;
            $payment->description = "";
            $payment->save();
        }

        return $transaction;
    }
}
