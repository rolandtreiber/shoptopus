<?php

namespace App\Services\Local\AccessToken;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\AccessToken\AccessTokenRepositoryInterface;

class AccessTokenService extends ModelService implements AccessTokenServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, AccessTokenRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'access_token');
    }
}
