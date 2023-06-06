<?php

namespace App\Services\Remote\Payment\Amazon;

use Amazon\Pay\API\Client;
use App\Exceptions\Payment\PaymentException;
use App\Repositories\Local\Transaction\Amazon\AmazonTransactionRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\Order\OrderServiceInterface;
use App\Services\Local\PaymentProvider\PaymentProviderService;
use Illuminate\Support\Facades\Storage;

/** AmazonPaymentService
 * @see https://amazonpaycheckoutintegrationguide.s3.amazonaws.com/amazon-pay-checkout/add-the-amazon-pay-button.html
 * 1 - Pass the id to the {{host}}/api/payment/amazon/settings?orderId={id} end-point
 * 2 - Take the response to set up the front-end client as seen in the development example views (http://localhost:port/dev/payments/amazon) also see view web.development.payments.amazon
 * 3 - Follow the JS steps in the integration guide above and finally pass the checkoutSessionID back to {{host}}/api/payment/execute/ see postman for details on payload, you can tell it to use the amazon addresses if you like, or set them yourself against the order.
 */
class AmazonPaymentService implements AmazonPaymentServiceInterface
{
    public bool $isProduction;

    private array $config;

    private Client $amazonClient;

    private ErrorServiceInterface $errorService;

    private PaymentProviderService $paymentProviderService;

    private OrderServiceInterface $orderService;

    private AmazonTransactionRepositoryInterface $transactionRepository;

    /**
     * @throws \Exception
     */
    public function __construct(
        ErrorServiceInterface $errorService,
        PaymentProviderService $paymentProviderService,
        AmazonTransactionRepositoryInterface $transactionRepository,
        OrderServiceInterface $orderService
    ) {
        $this->paymentProviderService = $paymentProviderService;
        $this->transactionRepository = $transactionRepository;
        $this->errorService = $errorService;
        $this->orderService = $orderService;

        $this->isProduction = app()->isProduction();

        $this->createClient();
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

            $payload = $this->getPayload($order);

            return [
                'merchantId' => $this->isProduction ? $this->config['MERCHANT_ID']['value'] : $this->config['MERCHANT_ID']['test_value'],
                'publicKeyId' => $this->isProduction ? $this->config['PUBLIC_KEY_ID']['value'] : $this->config['PUBLIC_KEY_ID']['test_value'],
                'ledgerCurrency' => $order['currency_code'],
                'checkoutLanguage' => 'en_GB',
                'productType' => 'PayOnly',
                'placement' => 'Checkout',
                'buttonColor' => 'Gold',
                'createCheckoutSessionConfig' => [
                    'payloadJSON' => $payload,
                    'signature' => $this->amazonClient->generateButtonSignature($payload),
                ],
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Execute payment
     *
     * @throws \Exception
     */
    public function executePayment(array $order, array $provider_payload): array
    {
        try {
            if (empty($provider_payload['checkout_session_id'])) {
                throw new PaymentException('Invalid checkout session id');
            }

            $payload = [
                'chargeAmount' => [
                    'amount' => $order['total_price'],
                    'currencyCode' => $order['currency_code'],
                ],
            ];

            $response = $this->amazonClient->completeCheckoutSession($provider_payload['checkout_session_id'], $payload);

            if ($response['status'] !== 200) {
                $decoded_res = json_decode($response['response']);

                throw new PaymentException($decoded_res->message, $response['status']);
            }

            return $this->transactionRepository->storeTransaction($this->validateResponse($response), $order['id']);
        } catch (PaymentException $e) {
            $this->errorService->logException($e, true);
            throw $e;
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
        $success = $executed_payment_response['status'] == 200;

        return [
            'success' => $success,
            'status_code' => $executed_payment_response['status'],
            'status' => $success ? 'CREATED' : 'NON_STANDARD',
            'payment_id' => json_decode($executed_payment_response['response'])->checkoutSessionId,
            'provider' => 'Amazon',
        ];
    }

    /**
     * Generate the Create Checkout Session payload
     * Returns the payload needed for the signature
     *
     * @see https://github.com/amzn/amazon-pay-api-sdk-php/issues/9
     */
    private function getPayload(array $order): array
    {
        $orderId = $order['id'];

        return [
            'webCheckoutDetails' => [
                'checkoutMode' => 'ProcessOrder',
                'checkoutResultReturnUrl' => $this->getCheckoutReturnUrl($orderId),
            ],
            'storeId' => $this->isProduction
                ? $this->config['STORE_ID']['value']
                : $this->config['STORE_ID']['test_value'],
            'paymentDetails' => [
                'paymentIntent' => 'AuthorizeWithCapture',
                'chargeAmount' => [
                    'amount' => $order['total_price'],
                    'currencyCode' => $order['currency_code'],
                ],
            ],
            'merchantMetadata' => [
                'merchantReferenceId' => $this->isProduction
                    ? 'transaction-'.$orderId
                    : 'test-transaction-'.$orderId,
                'merchantStoreName' => $this->isProduction
                    ? $this->config['STORE_NAME']['value']
                    : $this->config['STORE_NAME']['test_value'],
                'noteToBuyer' => 'Thank you for your purchase.',
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
     * Validate the paymentResponse object
     *
     *
     * @throws PaymentException
     */
    private function validateResponse(array $paymentResponse): array
    {
        if (! in_array($paymentResponse['status'], [200, 201])) {
            throw new PaymentException('Non-standard Amazon response code:'.$paymentResponse['status'].'Data: '.json_encode($paymentResponse['response']));
        }

        return $paymentResponse;
    }

    /**
     * Create the client via the Amazon API
     *
     *
     * @throws \Exception
     */
    private function createClient(): void
    {
        try {
            $settings = $this->paymentProviderService->get('amazon', 'name');
            $this->config = collect($settings['payment_provider_configs'])->keyBy('setting')->toArray();

            $this->amazonClient = new Client([
                'public_key_id' => $this->isProduction ? $this->config['PUBLIC_KEY_ID']['value'] : $this->config['PUBLIC_KEY_ID']['test_value'],
                'private_key' => Storage::disk('local')->get('/amazon/amazon_pay_private_key.pem'),
                'region' => $this->isProduction ? $this->config['REGION']['value'] : $this->config['REGION']['test_value'],
                // 'sandbox'       => !$this->isProduction,
                'sandbox' => true,
            ]);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }
}
