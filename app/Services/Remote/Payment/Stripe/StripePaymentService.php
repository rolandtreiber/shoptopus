<?php

namespace App\Services\Remote\Payment\Stripe;

use App\Models\Cart;
use App\Models\DeliveryType;
use App\Models\VoucherCode;
use App\Repositories\Local\Transaction\Stripe\StripeTransactionRepositoryInterface;
use App\Services\Local\Cart\CartService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\Order\OrderServiceInterface;
use App\Services\Local\PaymentProvider\PaymentProviderService;
use Stripe\PaymentIntent;
use Stripe\Stripe;

/** StripePaymentService
 * Limitations of finalising payments on the server see on below link
 *
 * @see https://stripe.com/docs/payments/accept-a-payment-synchronously
 * As an alternative we could be using webhooks to store the transaction, etc
 */
class StripePaymentService implements StripePaymentServiceInterface
{
    private array $config;

    private ErrorServiceInterface $errorService;

    private PaymentProviderService $paymentProviderService;

    private StripeTransactionRepositoryInterface $transactionRepository;

    public function __construct(
        ErrorServiceInterface                $errorService,
        PaymentProviderService               $paymentProviderService,
        StripeTransactionRepositoryInterface $transactionRepository,
    )
    {
        $this->errorService = $errorService;
        $this->paymentProviderService = $paymentProviderService;
        $this->transactionRepository = $transactionRepository;

        $this->config = collect($this->paymentProviderService->get('stripe', 'name')['payment_provider_configs'])
            ->keyBy('setting')
            ->toArray();
    }

    /**
     * Get the settings for a payment provider
     */
    public function getClientSettings(array $totals, Cart $cart, DeliveryType $deliveryType, VoucherCode|null $voucherCode): array
    {
        try {
            $this->setApiKey('secret_key');

            $intent = PaymentIntent::create([
                'amount' => ($totals['total_price'] + $deliveryType->price) * 100, // A positive integer representing how much to charge in the smallest currency unit (e.g., 100 cents to charge $1.00 or 100 to charge ¥100, a zero-decimal currency).
                'currency' => strtolower(config('app.default_currency.name')),
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $cart->user_id,
                    'delivery_type_id' => $deliveryType->id,
                    'voucher_code_id' => $voucherCode?->id,
                    'total_discount' => $totals['total_discount'],
                    'original_price' => $totals['original_price'],
                    'delivery' => $deliveryType->price
                ],
            ]);

            return [
                'publishableKey' => $this->getApikey("publishable_key"),
                'clientSecret' => $intent->client_secret,
                'order_total' => $intent->amount,
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Execute payment
     */
    public function executePayment(string $orderId, array $provider_payload): array
    {
        try {
            return $this->transactionRepository->storeTransaction($provider_payload, $orderId);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Format payment response
     */
    public function formatPaymentResponse(array $executed_payment_response): array
    {
        return [
            'success' => $executed_payment_response['status'] === 'succeeded',
            'status_code' => $executed_payment_response['status'] === 'succeeded' ? 200 : 500,
            'status' => $executed_payment_response['status'] === 'succeeded' ? 'CREATED' : 'NON_STANDARD',
            'payment_id' => $executed_payment_response['id'],
            'provider' => 'Stripe',
        ];
    }

    /**
     * Get the api key
     *
     *
     * @throws \Exception
     */
    private function getApiKey(string $type): string
    {
        try {
            return app()->isProduction() ? $this->config[$type]['value'] : $this->config[$type]['test_value'];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Set the api key for Stripe
     *
     * @throws \Exception
     */
    private function setApiKey(string $type)
    {
        try {
            Stripe::setApiKey($this->getApikey($type));
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }
}
