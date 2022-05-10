<?php

namespace App\Services\Remote\Payment\Stripe;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\Order\OrderServiceInterface;
use App\Services\Local\PaymentProvider\PaymentProviderService;
use App\Repositories\Local\Transaction\Stripe\StripeTransactionRepositoryInterface;

/** StripePaymentService
 * Limitations of finalising payments on the server see on below link
 * @see https://stripe.com/docs/payments/accept-a-payment-synchronously
 * As an alternative we could be using webhooks to store the transaction, etc
 */
class StripePaymentService implements StripePaymentServiceInterface
{
    private ErrorServiceInterface $errorService;
    private PaymentProviderService $paymentProviderService;
    private OrderServiceInterface $orderService;
    private StripeTransactionRepositoryInterface $transactionRepository;

    public function __construct(
        ErrorServiceInterface $errorService,
        PaymentProviderService $paymentProviderService,
        StripeTransactionRepositoryInterface $transactionRepository,
        OrderServiceInterface $orderService
    )
    {
        $this->errorService = $errorService;
        $this->paymentProviderService = $paymentProviderService;
        $this->transactionRepository = $transactionRepository;
        $this->orderService = $orderService;
    }

    /**
     * Get the settings for a payment provider
     *
     * @param string $orderId
     * @return array
     */
    public function getClientSettings(string $orderId) : array
    {
        try {
            $this->setApiKey();

            $order = $this->orderService->get($orderId);

            $intent = PaymentIntent::create([
                'amount' => $order['total_price'] * 100, // A positive integer representing how much to charge in the smallest currency unit (e.g., 100 cents to charge $1.00 or 100 to charge ¥100, a zero-decimal currency).
                'currency' => strtolower($order['currency_code']),
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order['id']
                ]
            ]);

            return [
                'publishableKey' => $this->getApikey(),
                'clientSecret' => $intent->client_secret,
                'order_total' => $intent->amount
            ];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Execute payment
     *
     * @param string $orderId
     * @param array $provider_payload
     * @return array
     */
    public function executePayment(string $orderId, array $provider_payload) : array
    {
        try {
            return $this->transactionRepository->storeTransaction($provider_payload, $orderId);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Format payment response
     *
     * @param array $executed_payment_response
     * @return array
     */
    public function formatPaymentResponse(array $executed_payment_response) : array
    {
       try {
           return [
               "success" => $executed_payment_response["status"] === "succeeded",
               "status_code" => $executed_payment_response["status"] === "succeeded" ? 200 : 500,
               "status" => $executed_payment_response["status"] === "succeeded" ? "CREATED" : "NON_STANDARD",
               "payment_id" => $executed_payment_response["id"],
               "provider" => "Stripe"
           ];
       } catch (\Exception | \Error $e) {
           $this->errorService->logException($e);
           throw $e;
       }
    }

    /**
     * Get the api key
     *
     * @param string $type
     * @return string
     * @throws \Exception
     */
    private function getApikey(string $type = 'publishable_key') : string
    {
        try {
            $settings = $this->paymentProviderService->get('stripe', 'name');
            $keyed_config = collect($settings["payment_provider_configs"])->keyBy('setting')->toArray();

            return app()->isProduction() ? $keyed_config[$type]["value"] : $keyed_config[$type]["test_value"];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Set the api key for Stripe
     *
     * @throws \Exception
     */
    private function setApiKey()
    {
        try {
            Stripe::setApiKey($this->getApikey('secret_key'));
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }
}
