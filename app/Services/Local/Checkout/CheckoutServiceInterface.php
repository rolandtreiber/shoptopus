<?php

namespace App\Services\Local\Checkout;

interface CheckoutServiceInterface
{
    public function createPendingOrderFromCart(array $payload): array;

    public function revertOrder(array $payload): array;

}
