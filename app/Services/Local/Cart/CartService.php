<?php

namespace App\Services\Local\Cart;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\Cart\CartRepositoryInterface;

class CartService extends ModelService implements CartServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, CartRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'cart');
    }
}
