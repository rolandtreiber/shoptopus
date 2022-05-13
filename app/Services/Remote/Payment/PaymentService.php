<?php

namespace App\Services\Remote\Payment;

//use App\Events\OrderCompleted;
use Illuminate\Support\Facades\Config;
use App\Exceptions\Payment\PaymentException;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\Order\OrderRepositoryInterface;
use App\Services\Remote\Payment\Stripe\StripePaymentServiceInterface;
use App\Services\Remote\Payment\PayPal\PayPalPaymentServiceInterface;
use App\Services\Remote\Payment\Amazon\AmazonPaymentServiceInterface;

class PaymentService implements PaymentServiceInterface
{
    private ErrorServiceInterface $errorService;
    private StripePaymentServiceInterface $stripePaymentService;
    private OrderRepositoryInterface $orderRepository;
    private PaypalPaymentServiceInterface $paypalPaymentService;
    private AmazonPaymentServiceInterface $amazonPaymentService;

    /**
     * @param ErrorServiceInterface $errorService
     * @param OrderRepositoryInterface $orderRepository
     * @param StripePaymentServiceInterface $stripePaymentService
     * @param PaypalPaymentServiceInterface $paypalPaymentService
     * @param AmazonPaymentServiceInterface $amazonPaymentService
     */
    public function __construct(
        ErrorServiceInterface $errorService,
        OrderRepositoryInterface $orderRepository,
        StripePaymentServiceInterface $stripePaymentService,
        PaypalPaymentServiceInterface $paypalPaymentService,
        AmazonPaymentServiceInterface $amazonPaymentService) {
        $this->errorService = $errorService;
        $this->orderRepository = $orderRepository;
        $this->stripePaymentService = $stripePaymentService;
        $this->paypalPaymentService = $paypalPaymentService;
        $this->amazonPaymentService = $amazonPaymentService;
    }

    /**
     * Get the settings for a payment provider
     *
     * @param string $provider
     * @param string $orderId
     * @return array
     * @throws \Exception
     */
    public function getClientSettings(string $provider, string $orderId) : array
    {
        try {
            $public_settings = match ($provider) {
                'stripe' => $this->stripePaymentService->getClientSettings($orderId),
                'paypal' => $this->paypalPaymentService->getClientSettings($orderId),
                'amazon' => $this->amazonPaymentService->getClientSettings($orderId),
                default => throw new \Exception("Please pass in a valid payment provider"),
            };

            return $public_settings;
        } catch (\Exception | \Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.getClientSettings'));
        }
    }

    /**
     * Execute a payment using the correct gateway
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function executePayment(array $payload) : array
    {
        try {
            $provider = $payload['provider'];
            $orderId = $payload['orderId'];
            $provider_payload = $payload['provider_payload'] ?? [];

            $order = $this->orderRepository->get($orderId, 'id', ['products']);

            $executed_payment_response = match ($provider) {
                'stripe' => $this->stripePaymentService->executePayment($order['id'], $provider_payload),
                'paypal' => $this->paypalPaymentService->executePayment($order['id'], $provider_payload),
                'amazon' => $this->amazonPaymentService->executePayment($order, $provider_payload),
                default => throw new \Exception("Please pass in a valid payment provider, order id: $orderId"),
            };

            $response = $this->formatPaymentResponse($provider, $executed_payment_response);

            if($response["success"]) {
                //OrderCompleted::dispatch($order['id'], $response);
            }

            return $response;
        } catch (PaymentException $e) {
            $this->errorService->logException($e, true);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        } catch (\Exception | \Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        }
    }

    /**
     * Return a uniform response object from the API
     *
     * @param string $provider
     * @param array $executed_payment_response
     * @return array
     * @throws \Exception
     */
    public function formatPaymentResponse(string $provider, array $executed_payment_response) : array
    {
        try {
            $api_response_payload = match ($provider) {
                'stripe' => $this->stripePaymentService->formatPaymentResponse($executed_payment_response),
                'paypal' => $this->paypalPaymentService->formatPaymentResponse($executed_payment_response),
                'amazon' => $this->amazonPaymentService->formatPaymentResponse($executed_payment_response),
                default => throw new \Exception("Please pass in a valid payment provider")
            };

            return $api_response_payload;
        } catch (\Exception | \Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.formatPaymentResponse'));
        }
    }
}
