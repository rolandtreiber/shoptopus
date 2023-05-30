<?php

namespace App\Services\Remote\Payment\Amazon;

interface AmazonPaymentServiceInterface
{
    /**
     * Get the settings for a payment provider
     *
     *
     * @throws \Exception
     */
    public function getClientSettings(string $orderId): array;

    /**
     * Execute payment
     */
    public function executePayment(array $order, array $provider_payload): array;

    /**
     * Format payment response
     */
    public function formatPaymentResponse(array $executed_payment_response): array;
}
