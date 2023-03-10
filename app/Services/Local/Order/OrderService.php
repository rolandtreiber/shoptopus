<?php

namespace App\Services\Local\Order;

use App\Repositories\Local\Order\OrderRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class OrderService extends ModelService implements OrderServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, OrderRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'voucher_code');
    }
}
