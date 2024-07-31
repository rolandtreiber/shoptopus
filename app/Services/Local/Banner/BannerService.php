<?php

namespace App\Services\Local\Banner;

use App\Repositories\Local\Banner\BannerRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class BannerService extends ModelService implements BannerServiceInterface
{

    public function __construct(ErrorServiceInterface $errorService, BannerRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'banner');
    }

}
