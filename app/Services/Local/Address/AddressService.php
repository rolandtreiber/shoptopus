<?php

namespace App\Services\Local\Address;

use App\Repositories\Local\Address\AddressRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class AddressService extends ModelService implements AddressServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, AddressRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'address');
    }
}
