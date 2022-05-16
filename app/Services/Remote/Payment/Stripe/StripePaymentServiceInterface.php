<?php

namespace App\Services\Remote\Payment\Stripe;

interface StripePaymentServiceInterface {

    /**
     * Get the settings for a payment provider
     *
     * @param string $orderId
     * @return array
     */
    public function getClientSettings(string $orderId) : array;

    /**
     * Execute payment
     *
     * @param string $orderId
     * @param array $provider_payload
     * @return array
     */
    public function executePayment(string $orderId, array $provider_payload) : array;

    /**
     * Format payment response
     *
     * @param array $executed_payment_response
     * @return array
     */
    public function formatPaymentResponse(array $executed_payment_response) : array;

}
