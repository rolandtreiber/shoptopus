<?php

namespace App\Repositories\Local\Address;

use App\Models\Address;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\User\UserServiceInterface;
use App\Services\Local\Error\ErrorServiceInterface;

class AddressRepository extends ModelRepository implements AddressRepositoryInterface
{
    private UserServiceInterface $userService;

    public function __construct(ErrorServiceInterface $errorService, Address $model, UserServiceInterface $userServiceInterface)
    {
        parent::__construct($errorService, $model);

        $this->userService = $userServiceInterface;
    }

    /**
     * Get the user for the given address
     *
     * @param array $userIds
     * @return array
     * @throws \Exception
     */
    public function getUsers(array $userIds = []) : array
    {
        $result = $this->userService->getAll([], [
            'id' => implode(',', $userIds)
        ]);

        return !empty($result['data']) ? $result['data'] : [];
    }

    /**
     * Get the required related models for the given parent
     *
     * @param $result
     * @param array $excludeRelationships
     * @return array
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []) : array
    {
        if (!in_array('user', $excludeRelationships)) {
            $users = $this->getUsers(collect($result)->pluck('user_id')->toArray());

            foreach($result as &$model) {
                $model['user'] = [];
                foreach ($users as $user) {
                    if($user['id'] === $model['user_id']) {
                        $model['user'] = $user;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.user_id",
            "{$this->model_table}.name",
            "{$this->model_table}.address_line_1",
            "{$this->model_table}.address_line_2",
            "{$this->model_table}.town",
            "{$this->model_table}.post_code",
            "{$this->model_table}.country",
            "{$this->model_table}.lat",
            "{$this->model_table}.lon",
            "{$this->model_table}.deleted_at"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
