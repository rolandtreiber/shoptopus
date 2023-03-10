<?php

namespace App\Repositories\Local\AccessToken;

use App\Models\AccessToken;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class AccessTokenRepository extends ModelRepository implements AccessTokenRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, AccessToken $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the columns for selection
     *
     * @param  bool  $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.type",
            "{$this->model_table}.token",
            "{$this->model_table}.user_id",
            "{$this->model_table}.issuer_user_id",
            "{$this->model_table}.expiry",
            "{$this->model_table}.created_at",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }
}
