<?php

namespace App\Services\Local\Cart;

use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\Cart\CartRepositoryInterface;

class CartService extends ModelService implements CartServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, CartRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'cart');
    }

    /**
     * Get the user's cart
     *
     * @param string $userId
     * @return array
     * @throws \Exception
     */
    public function getCartForUser(string $userId) : array
    {
        try {
            return $this->modelRepository->getCartForUser($userId);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.cart.getCartForUser"));
        }
    }
}
