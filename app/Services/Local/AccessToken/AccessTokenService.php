<?php

namespace App\Services\Local\AccessToken;

use App\Repositories\Local\AccessToken\AccessTokenRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class AccessTokenService extends ModelService implements AccessTokenServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, AccessTokenRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'access_token');
    }
}
