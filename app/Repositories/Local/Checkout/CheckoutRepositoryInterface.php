<?php

namespace App\Repositories\Local\Checkout;

interface CheckoutRepositoryInterface
{
    public function createPendingOrderFromCart(array $payload): array;

    public function revertOrder(array $payload): array;
}
