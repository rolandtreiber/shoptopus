<?php

namespace App\Services\Local\VoucherCode;

use App\Repositories\Local\VoucherCode\VoucherCodeRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class VoucherCodeService extends ModelService implements VoucherCodeServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, VoucherCodeRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'voucher_code');
    }
}
