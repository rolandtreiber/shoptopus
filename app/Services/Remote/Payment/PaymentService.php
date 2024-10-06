<?php

namespace App\Services\Remote\Payment;

//use App\Events\OrderCompleted;
use App\Enums\OrderStatus;
use App\Exceptions\CheckoutException;
use App\Exceptions\Payment\PaymentException;
use App\Models\Cart;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\VoucherCode;
use App\Repositories\Local\Order\OrderRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Remote\Payment\Amazon\AmazonPaymentServiceInterface;
use App\Services\Remote\Payment\PayPal\PayPalPaymentServiceInterface;
use App\Services\Remote\Payment\Stripe\StripePaymentServiceInterface;
use Illuminate\Support\Facades\Config;

class PaymentService implements PaymentServiceInterface
{
    private ErrorServiceInterface $errorService;

    private StripePaymentServiceInterface $stripePaymentService;

    private OrderRepositoryInterface $orderRepository;

    private PaypalPaymentServiceInterface $paypalPaymentService;

    private AmazonPaymentServiceInterface $amazonPaymentService;

    public function __construct(
        ErrorServiceInterface $errorService,
        OrderRepositoryInterface $orderRepository,
        StripePaymentServiceInterface $stripePaymentService,
        PaypalPaymentServiceInterface $paypalPaymentService,
        AmazonPaymentServiceInterface $amazonPaymentService)
    {
        $this->errorService = $errorService;
        $this->orderRepository = $orderRepository;
        $this->stripePaymentService = $stripePaymentService;
        $this->paypalPaymentService = $paypalPaymentService;
        $this->amazonPaymentService = $amazonPaymentService;
    }

    /**
     * Get the settings for a payment provider
     *
     *
     * @throws \Exception
     */
    public function getClientSettings(string $provider, array $payload): array
    {
        try {
            $order = null;
            $voucherCode = null;

            // Scenario 1:
            // We do not have an order created yet.
            // It is the case for stripe whereby the widget creation requires a payment intent,
            // however creating an order implies removing the products from the cart which in turn leads to erroneous
            // behaviour if the user closes the browser without attempting the payment.
            if ($provider === "stripe") {
                if (!array_key_exists('cartId', $payload) || !array_key_exists('deliveryTypeId', $payload)) {
                    throw new CheckoutException("Invalid payload for Stripe");
                } else {
                    $cart = Cart::find($payload['cartId']);
                    $deliveryType = DeliveryType::find($payload['deliveryTypeId']);
                    if (array_key_exists('voucherCode', $payload) && $payload['voucherCode'] !== null) {
                        $voucherCode = VoucherCode::where('code', $payload['voucherCode'])->first();
                    }
                    if (!$cart) {
                        throw new CheckoutException("Invalid cart");
                    }
                    if (!$deliveryType || !$deliveryType->enabled) {
                        throw new CheckoutException("Invalid delivery type");
                    }
                    $totals = $cart->getTotals($voucherCode);

                    if (!array_key_exists('original_price', $totals) || !array_key_exists('total_price', $totals) || !array_key_exists('total_discount', $totals)) {
                        throw new CheckoutException("Unable to get totals. Possibly corrupted data.");
                    }
                }
            }

            // Scenario 2:
            // We do have an order. In this case the delivery type, voucher code and totals can all be derived from the order.

            if (array_key_exists('orderId', $payload)) {
                $order = $payload['orderId'];
            }

            $public_settings = match ($provider) {
                // @phpstan-ignore-next-line (the logic is sound, the cart variable is only used if provider is stripe)
                'stripe' => $this->stripePaymentService->getClientSettings($totals, $cart, $deliveryType, $voucherCode),
                'paypal' => $this->paypalPaymentService->getClientSettings($order),
                'amazon' => $this->amazonPaymentService->getClientSettings($order),
                default => throw new \Exception('Please pass in a valid payment provider'),
            };

            return $public_settings;
        } catch (\Exception|\Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.getClientSettings'));
        }
    }

    /**
     * Execute a payment using the correct gateway
     *
     *
     * @throws \Exception
     */
    public function executePayment(array $payload): array
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

            if ($response['success']) {
                Order::find($orderId)->update(['status' => OrderStatus::Paid]);
                //OrderCompleted::dispatch($order['id'], $response);
            }

            return $response;
        } catch (PaymentException $e) {
            $this->errorService->logException($e, true);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        } catch (\Exception|\Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        }
    }

    /**
     * Return a uniform response object from the API
     *
     *
     * @throws \Exception
     */
    public function formatPaymentResponse(string $provider, array $executed_payment_response): array
    {
        try {
            $api_response_payload = match ($provider) {
                'stripe' => $this->stripePaymentService->formatPaymentResponse($executed_payment_response),
                'paypal' => $this->paypalPaymentService->formatPaymentResponse($executed_payment_response),
                'amazon' => $this->amazonPaymentService->formatPaymentResponse($executed_payment_response),
                default => throw new \Exception('Please pass in a valid payment provider')
            };

            return $api_response_payload;
        } catch (\Exception|\Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.formatPaymentResponse'));
        }
    }
}
