<?php

namespace App\Services\Local\User;

use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\User\UserRepositoryInterface;

class UserService extends ModelService implements UserServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, UserRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'user');
    }

    /**
     * get currently authenticated user details.
     * @return array|bool
     * @throws \Exception
     */
    public function getCurrentUser() : ?array
    {
        try {
            return $this->modelRepository->getCurrentUser();
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getCurrentUser'));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getCurrentUser'));
        }
    }

    /**
     * get a user model by its email
     * @param string $email
     * @return mixed
     * @throws \Exception
     */
    public function getByEmail(string $email) : array
    {
        try {
            return $this->modelRepository->getByEmail($email);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.user.getByEmail"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.user.getByEmail"));
        }
    }
}
