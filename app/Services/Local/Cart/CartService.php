<?php

namespace App\Services\Local\Cart;

use App\Repositories\Local\Cart\CartRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;

class CartService extends ModelService implements CartServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, CartRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'cart');
    }

    /**
     * Get the user's cart
     *
     *
     * @throws \Exception
     */
    public function getCartForUser(string $userId): array
    {
        try {
            return $this->modelRepository->getCartForUser($userId);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.cart.getCartForUser'));
        }
    }

    /**
     * Add item to cart.
     *
     *
     * @throws \Exception
     */
    public function addItem(array $payload): array
    {
        try {
            return $this->modelRepository->addItem($payload);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.cart.addItem'));
        }
    }

    /**
     * Remove item from cart.
     *
     *
     * @throws \Exception
     */
    public function removeItem(array $payload): array
    {
        try {
            return $this->modelRepository->removeItem($payload);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.cart.removeItem'));
        }
    }

    /**
     * Remove item from cart.
     *
     *
     * @throws \Exception
     */
    public function removeAll(array $payload): array
    {
        try {
            return $this->modelRepository->removeAll($payload);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.cart.removeAll'));
        }
    }

    /**
     * Update quantity for a given product
     *
     *
     * @throws \Exception
     */
    public function updateQuantity(array $payload): array
    {
        try {
            return $this->modelRepository->updateQuantity($payload);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.cart.updateQuantity'));
        }
    }

    /**
     * Merge the user's carts
     *
     *
     * @throws \Exception
     */
    public function mergeUserCarts(string $userId, string $cartId): array
    {
        try {
            return $this->modelRepository->mergeUserCarts($cartId, $userId);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.cart.mergeUserCarts'));
        }
    }
}
