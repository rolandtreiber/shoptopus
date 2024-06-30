<?php

namespace App\Repositories\Local\Checkout;

use App\Repositories\Local\Checkout\CheckoutRepositoryInterface;

class CheckoutRepository implements CheckoutRepositoryInterface
{

    public function createPendingOrderFromCart(array $payload): array
    {
        return [];
    }

    public function revertOrder(array $payload): array
    {
        return [];
    }

    public function getAvailableDeliveryTypesForAddress(array $payload): array
    {
        return [];
    }
}
