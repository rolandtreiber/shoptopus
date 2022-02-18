<?php

namespace App\Services\Local\Order;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\Order\OrderRepositoryInterface;

class OrderService extends ModelService implements OrderServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, OrderRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'voucher_code');
    }
}
