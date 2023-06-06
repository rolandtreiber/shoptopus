<?php

namespace App\Services\Remote\Payment\PayPal;

interface PayPalPaymentServiceInterface
{
    /**
     * Get the settings for a payment provider
     *
     *
     * @throws \Exception
     */
    public function getClientSettings(string $orderId): array;

    /**
     * Create a PayPal Order/Payment
     */
    public function executePayment(string $orderId, array $provider_payload): array;

    /**
     * Format payment response
     */
    public function formatPaymentResponse(array $executed_payment_response): array;
}
