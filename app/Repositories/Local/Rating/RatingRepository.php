<?php

namespace App\Repositories\Local\Rating;

use App\Models\Rating;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class RatingRepository extends ModelRepository implements RatingRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Rating $model)
    {
        parent::__construct($errorService, $model);
    }

    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array
    {
        $ids = collect($result)->pluck('id')->toArray();
        return $result;
    }
        public function getSelectableColumns(bool $withTableNamePrefix = true): array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.user_id",
            "{$this->model_table}.slug",
            "{$this->model_table}.title",
            "{$this->model_table}.description",
            "{$this->model_table}.rating",
            "{$this->model_table}.language_prefix",
            "{$this->model_table}.ratable_id",
            "{$this->model_table}.enabled",
            "{$this->model_table}.verified",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }

    public function getAllForProduct(string $productId, array $ratings = [], string $languagePrefix = "en", array $page_formatting = []): array
    {
        $queryParams = [];
        try {
            $queryParams[] = $productId;
            $filter_string = ' WHERE ratings.ratable_type = "App\\\Models\\\Product"';
            $filter_string .= ' AND ratings.ratable_id = ?';
            if (count($ratings) > 0) {
                $dynamic_placeholders = trim(str_repeat('?,', count($ratings)), ',');
                $filter_string .= " AND ratings.rating IN ({$dynamic_placeholders})";
                $queryParams = array_merge($queryParams, $ratings);
            }
            $filter_string .= ' AND ratings.language_prefix = ?';
            $filter_string .= ' AND ratings.enabled IS TRUE';
            $filter_string .= ' AND ratings.deleted_at IS NULL';

            $queryParams[] = $languagePrefix;
            $filter_vars = (object) [
                'filter_string' => $filter_string,
                'query_parameters' => $queryParams,
            ];

            $filters = ['custom_filter_vars' => $filter_vars];

            return $this->getAll($page_formatting, $filters);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }


}
