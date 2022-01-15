<?php

namespace App\Repositories\Local\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class UserRepository extends ModelRepository implements UserRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, User $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * get the currently authenticated user instance.
     * @param bool $returnAsArray
     * @return null|mixed
     */
    public function getCurrentUser(bool $returnAsArray = true)
    {
        try {
            $user = Auth::check() ? Auth::user() : null;

            if (!$user) {
                return null;
            }

            return $returnAsArray ? $user->toArray() : $user;
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * get a user model by its email
     * @param string $email
     * @return array
     * @throws \Exception
     */
    public function getByEmail(string $email) : array
    {
        try {
            $filters = ['email' => $email];

            $page_formatting = ['limit' => 1];

            $result = $this->getModels($this->getFilters($filters, $this->model_table), $page_formatting, []);

            return !empty($result) ? $result[0] : [];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * get the columns for selection
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.name",
            "{$this->model_table}.first_name",
            "{$this->model_table}.last_name",
            "{$this->model_table}.email",
            "{$this->model_table}.phone",
            "{$this->model_table}.email_verified_at",
            "{$this->model_table}.avatar",
            "{$this->model_table}.client_ref",
            "{$this->model_table}.client_ref",
            "{$this->model_table}.deleted_at"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
