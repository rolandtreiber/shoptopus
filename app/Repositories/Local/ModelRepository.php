<?php

namespace App\Repositories\Local;

use App\Services\Local\Error\ErrorServiceInterface;
use App\Traits\FilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModelRepository implements ModelRepositoryInterface
{
    use FilterTrait;

    protected $model;

    protected string $model_table;

    protected ErrorServiceInterface $errorService;

    public function __construct($errorService, $model)
    {
        $this->errorService = $errorService;
        $this->model = $model;
        $this->model_table = $model->getTable();
    }

    /**
     * Get all models
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array
    {
        try {
            if (isset($filters['custom_filter_vars'])) {
                $filter_vars = $filters['custom_filter_vars'];
            } else {
                if ($this->canBeSoftDeleted()) {
                    $filters['deleted_at'] = 'null';
                }

                if ($this->hasActiveProperty()) {
                    $filters['active'] = 1;
                } elseif ($this->hasEnabledProperty()) {
                    $filters['enabled'] = 1;
                }

                $filter_vars = $this->getFilters($this->model_table, $filters);
            }

            return [
                'data' => $this->getModels($filter_vars, $page_formatting, $excludeRelationships),
                'count' => $this->getCount($filter_vars),
            ];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get a single model
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []): array
    {
        try {
            $filters = [$key => $value];

            if ($this->canBeSoftDeleted()) {
                $filters['deleted_at'] = 'null';
            }

            if ($this->hasActiveProperty()) {
                $filters['active'] = 1;
            } elseif ($this->hasEnabledProperty()) {
                $filters['enabled'] = 1;
            }

            $page_formatting = ['limit' => 1];

            $result = $this->getModels($this->getFilters($this->model_table, $filters), $page_formatting, $excludeRelationships);

            return ! empty($result) ? $result[0] : [];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Create a model
     */
    public function post(array $payload, bool $returnAsArray = true): mixed
    {
        try {
            $model = $this->model->create($payload);

            if (method_exists($this, 'saveRelationships')) {
                $this->saveRelationships($model->id, $payload);
            }

            return $returnAsArray
                ? $model->toArray()
                : $model;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Update a model
     *
     *
     * @throws \Exception
     */
    public function update(string $id, array $payload): mixed
    {
        try {
            $model = $this->model->find($id);

            $model->update($payload);

            if (method_exists($this, 'saveRelationships')) {
                $this->saveRelationships($model->id, $payload);
            }

            return $model->toArray();
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Delete a model
     *
     *
     * @throws \Exception
     */
    public function delete(string $id): int
    {
        try {
            $query = DB::table($this->model_table)->where('id', $id);

            return $this->canBeSoftDeleted()
                ? $query->update(['deleted_at' => now()])
                : $query->delete();
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Return the models based on the filters and apply page formatting if applicable
     */
    public function getModels($filter_vars, array $page_formatting = [], array $excludeRelationships = []): array
    {
        try {
            if (! empty($page_formatting)) {
                $order_by_string = ' '.$this->getOrderByString($page_formatting).' LIMIT ?, ?;';
                $query_params = $this->getQueryParams($filter_vars, $page_formatting);
            } else {
                $order_by_string = '';
                $query_params = $filter_vars->query_parameters;
            }

            //DB::enableQueryLog();

            $sql = 'SELECT ';
            $sql .= implode(',', $this->getSelectableColumns());
            $sql .= " FROM $this->model_table";
            $sql .= $filter_vars->filter_string;
            $sql .= $order_by_string;
            $result = DB::select($sql, $query_params);

            //dd(DB::getQueryLog());

            return $result && method_exists($this, 'getTheResultWithRelationships')
                ? $this->getTheResultWithRelationships($result, $excludeRelationships)
                : $result;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the records count
     */
    public function getCount($filter_vars): int
    {
        $sql = "SELECT count(*) AS count FROM $this->model_table $filter_vars->filter_string";
        $result = DB::select($sql, $filter_vars->query_parameters);

        return ! empty($result) ? (int) $result[0]['count'] : 0;
    }

    /**
     * Determine if the model has an active property
     */
    protected function hasActiveProperty(): bool
    {
        return in_array('active', Schema::getColumnListing($this->model_table));
    }

    /**
     * Determine if the model has an enabled property
     */
    protected function hasEnabledProperty(): bool
    {
        return in_array('enabled', Schema::getColumnListing($this->model_table));
    }

    /**
     * Determine if the model has a soft deleted property
     */
    protected function canBeSoftDeleted(): bool
    {
        return in_array('deleted_at', Schema::getColumnListing($this->model_table));
    }
}
