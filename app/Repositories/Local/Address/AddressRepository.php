<?php

namespace App\Repositories\Local\Address;

use App\Models\Address;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\DB;

class AddressRepository extends ModelRepository implements AddressRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Address $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the users for the given addresses
     *
     *
     * @throws \Exception
     */
    public function getUsers(array $userIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($userIds)), ',');

            return DB::select("
                SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.name,
                    u.initials,
                    u.prefix,
                    u.phone,
                    u.avatar,
                    u.email_verified_at,
                    u.client_ref,
                    u.temporary,
                    u.is_favorite
                FROM users AS u
                WHERE u.id IN ($dynamic_placeholders)
                AND u.deleted_at IS NULL
            ", $userIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the required related models for the given parent
     *
     *
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array
    {
        $ids = collect($result)->unique('user_id')->pluck('user_id')->toArray();

        $users = [];

        try {
            if (! in_array('user', $excludeRelationships)) {
                $users = $this->getUsers($ids);
                $model['user'] = null;
            }

            foreach ($result as &$model) {

                foreach ($users as $user) {
                    if ($user['id'] === $model['user_id']) {
                        $model['user'] = $user;
                    }
                }
            }

            return $result;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array
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
            "{$this->model_table}.deleted_at",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }
}
