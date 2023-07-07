<?php

namespace App\Services\Remote\Payment\PayPal;

use App\Exceptions\Payment\PaymentException;
use App\Repositories\Local\Transaction\PayPal\PayPalTransactionRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\Order\OrderServiceInterface;
use App\Services\Local\PaymentProvider\PaymentProviderService;
use Illuminate\Support\Facades\Config;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpResponse;

/**
 * PayPalPaymentService
 *
 * @see https://github.com/paypal/Checkout-PHP-SDK
 * @see https://developer.paypal.com/docs/checkout/reference/server-integration/setup-sdk/
 * @see https://developer.paypal.com/docs/archive/checkout/how-to/server-integration/
 * 1 - Open the order with PayPal and get the clientID for setting up the client using the following request: {{host}}/api/payment/paypal/settings?data[orderId]={id}
 * 2 - Have the user approve the order by sending the to the approval URL sent in the above request
 * 3 - Pass the token from the return URL to the {{host}}/api/payment/execute/ end-point (see postman for details of payload)
 */
class PayPalPaymentService implements PayPalPaymentServiceInterface
{
    private array $config;

    private ErrorServiceInterface $errorService;

    private PaymentProviderService $paymentProviderService;

    private PayPalTransactionRepositoryInterface $transactionRepository;

    private OrderServiceInterface $orderService;

    public function __construct(
        ErrorServiceInterface $errorService,
        PaymentProviderService $paymentProviderService,
        PayPalTransactionRepositoryInterface $transactionRepository,
        OrderServiceInterface $orderService
    ) {
        $this->paymentProviderService = $paymentProviderService;
        $this->transactionRepository = $transactionRepository;
        $this->errorService = $errorService;
        $this->orderService = $orderService;

        $this->config = collect($this->paymentProviderService->get('paypal', 'name')['payment_provider_configs'])
            ->keyBy('setting')
            ->toArray();
    }

    /**
     * Get the settings for a payment provider
     *
     *
     * @throws \Exception
     */
    public function getClientSettings(string $orderId): array
    {
        try {
            $order = $this->orderService->get($orderId);

            return [
                'client_id' => app()->isProduction()
                    ? $this->config['CLIENT_ID']['value']
                    : $this->config['CLIENT_ID']['test_value'],
                'pay_pal_order_creation' => $this->createOrder($order),
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        }
    }

    /**
     * Create a PayPal Order/Payment
     *
     *
     * @throws \Exception
     */
    public function executePayment(string $orderId, array $provider_payload): array
    {
        try {
            $request = new OrdersCaptureRequest($provider_payload[0]['paypal_order_id_token']);
            $request->prefer('return=representation');

            return (array) $this->transactionRepository->storeTransaction(
                $this->validateResponse($this->client()->execute($request)), $orderId
            );
        } catch (\PayPalHttp\HttpException $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage() ?: 'Client Authentication failed.', Config::get('api_error_codes.services.remote.payment.execute'));
        } catch (PaymentException $e) {
            $this->errorService->logException($e, true);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        }
    }

    /**
     * Format payment response
     */
    public function formatPaymentResponse(array $executed_payment_response): array
    {
        return [
            'success' => $executed_payment_response['statusCode'] == 201,
            'status_code' => $executed_payment_response['statusCode'],
            'status' => $executed_payment_response['result']->status,
            'payment_id' => $executed_payment_response['result']->id,
            'provider' => 'PayPal',
        ];
    }

    /**
     * Create a PayPal Order/Payment
     *
     *
     * @throws \Exception
     */
    private function createOrder(array $order): HttpResponse
    {
        try {
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = $this->buildCreateOrderRequestBody($order);

            return $this->validateResponse($this->client()->execute($request));
        } catch (PaymentException $e) {
            $this->errorService->logException($e, true);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        }
    }

    /**
     * ValidateResponse
     *
     *
     * @throws PaymentException
     *
     * @todo - capture more error codes from PayPal - for example, system error, we need to let errors like "no funds" etc pass back to the front-end
     */
    private function validateResponse(object $response): HttpResponse
    {
        if (! $response instanceof HttpResponse) {
            throw new PaymentException("This is not an instance of PayPalHttp\HttpResponse");
        }

        if (! in_array($response->statusCode, [200, 201])) {
            throw new PaymentException("Non-standard PayPal response code: $response->statusCode Data: ".json_encode($response->result));
        }

        return $response;
    }

    /**
     * Setting up the JSON request body for creating the order with minimum request body. The intent in the
     * request body should be "AUTHORIZE" for authorize intent flow.
     */
    private function buildCreateOrderRequestBody(array $order): array
    {
        $checkout_url = $this->getCheckoutReturnUrl($order['id']);

        return [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => $checkout_url,
                'cancel_url' => $checkout_url,
            ],
            'purchase_units' => [
                [
                    'reference_id' => app()->isProduction()
                        ? 'transaction-'.$order['id']
                        : 'test-transaction-'.$order['id'],
                    'amount' => [
                        'currency_code' => $order['currency_code'],
                        'value' => $order['total_price'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the return url
     */
    private function getCheckoutReturnUrl(string $orderId): string
    {
        return config('app.frontend_url_public').config('payment.return_path')."?order_id={$orderId}";
    }

    /**
     * Returns PayPal HTTP client instance with environment that has access
     * credentials context. Use this instance to invoke PayPal APIs, provided the
     * credentials have access.
     *
     *
     * @throws \Exception
     */
    private function client(): PayPalHttpClient
    {
        try {
            return new PayPalHttpClient($this->environment());
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        }
    }

    /**
     * Set up and return PayPal PHP SDK environment with PayPal access credentials.
     * This sample uses SandboxEnvironment. In production, use ProductionEnvironment.
     *
     *
     * @throws \Exception
     */
    private function environment(): ProductionEnvironment|SandboxEnvironment
    {
        try {
            if (app()->isProduction()) {
                $clientId = $this->config['CLIENT_ID']['value'] ?: null;
                $clientSecret = $this->config['SECRET']['value'] ?: null;
                if (config('app.env') !== 'production') {
                    return new SandboxEnvironment($clientId, $clientSecret);
                } else {
                    return new ProductionEnvironment($clientId, $clientSecret);
                }
            } else {
                $clientId = $this->config['CLIENT_ID']['test_value'] ?: null;
                $clientSecret = $this->config['SECRET']['test_value'] ?: null;

                return new SandboxEnvironment($clientId, $clientSecret);
            }
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.remote.payment.execute'));
        }
    }
}
