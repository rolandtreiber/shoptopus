<?php

namespace App\Repositories\Local;

use App\Traits\FilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModelRepository implements ModelRepositoryInterface
{
    use FilterTrait;

    protected $model;
    protected $model_table;
    protected $errorService;

    public function __construct($errorService, $model) {
        $this->errorService = $errorService;
        $this->model = $model;
        $this->model_table = $model->getTable();
    }

    /**
     * get all models
     * @param array $page_formatting
     * @param array $filters
     * @param array $excludeRelationships
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []) : array
    {
        try {
            $filter_vars = $this->getFilters($filters, $this->model_table);

            return [
                'data' => $this->getModels($filter_vars, $page_formatting, $excludeRelationships),
                'count' => $this->getCount($filter_vars)
            ];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * get a single model
     * @param int $id
     * @param array $excludeRelationships
     * @return array
     */
    public function get(int $id, array $excludeRelationships = []) : array
    {
        try {
            $filters = ['id' => $id];

            if ($this->canBeSoftDeleted()) {
                $filters['deleted_at'] = 'null';
            }

            if ($this->hasActiveProperty()) {
                $filters['active'] = 1;
            }
            $page_formatting = ['limit' => 1];

            $result = $this->getModels($this->getFilters($filters, $this->model_table), $page_formatting, $excludeRelationships);

            return !empty($result) ? $result[0] : [];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * get a single model by its slug
     * @param string $slug
     * @param array $excludeRelationships
     * @return array
     */
    public function getBySlug(string $slug, array $excludeRelationships = []) : array
    {
        try {
            $filters = ['slug' => $slug];

            if ($this->canBeSoftDeleted()) {
                $filters['deleted_at'] = 'null';
            }

            if ($this->hasActiveProperty()) {
                $filters['active'] = 1;
            }

            $page_formatting = ['limit' => 1];

            $result = $this->getModels($this->getFilters($filters, $this->model_table), $page_formatting, $excludeRelationships);

            return !empty($result) ? $result[0] : [];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * get a single model by its uuid
     * @param string $uuid
     * @param array $excludeRelationships
     * @return array
     * @throws \Exception
     */
    public function getByUuid(string $uuid, array $excludeRelationships = []) : array
    {
        try {
            $filters = ['uuid' => $uuid];

            if ($this->canBeSoftDeleted()) {
                $filters['deleted_at'] = 'null';
            }

            $page_formatting = ['limit' => 1];

            $result = $this->getModels($this->getFilters($filters, $this->model_table), $page_formatting, $excludeRelationships);

            return !empty($result) ? $result[0] : [];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * create a model
     * @param array $payload
     * @param bool $returnAsArray
     * @return mixed
     */
    public function post(array $payload, bool $returnAsArray = true)
    {
        try {
            $model = $this->model->create($payload);

            if(method_exists($this, 'saveRelationships')) {
                $this->saveRelationships($model->id, $payload);
            }

            return $returnAsArray
                ? $model->toArray()
                : $model;
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * update a model
     * @param int $id
     * @param array $payload
     * @return mixed
     * @throws \Exception
     */
    public function update(int $id, array $payload)
    {
        try {
            $model = $this->model->find($id);

            $model->update($payload);

            if(method_exists($this, 'saveRelationships')) {
                $this->saveRelationships($model->id, $payload);
            }

            return $model->toArray();
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * delete a model
     * @param int $id
     * @return int
     * @throws \Exception
     */
    public function delete(int $id) : int
    {
        try {
            $query = DB::table($this->model_table)->where('id', $id);

            return $this->canBeSoftDeleted()
                ? $query->update(['deleted_at' => now()])
                : $query->delete();
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Return the models based on the filters and apply page formatting if applicable
     * @param $filter_vars
     * @param array $page_formatting
     * @param array $excludeRelationships
     * @return array
     */
    public function getModels($filter_vars, array $page_formatting = [], array $excludeRelationships = []) : array
    {
        try {
            $hasPageFormatting = !empty($page_formatting);

            $order_by_string = $hasPageFormatting ? $this->getOrderByString($page_formatting) : null;
            $query_params = $hasPageFormatting ? $this->getQueryParams($filter_vars, $page_formatting) : null;

//            DB::enableQueryLog();

            $sql = "SELECT ";
            $sql .= implode(',', $this->getSelectableColumns());
            $sql .= " FROM $this->model_table ";
            $sql .= $filter_vars->filter_string;
            $sql .= $hasPageFormatting ? " $order_by_string LIMIT ?, ?;" : "";
            $result = DB::select($sql, $hasPageFormatting ? $query_params : $filter_vars->query_parameters);

//            dd(DB::getQueryLog());

            return $result && method_exists($this,'getTheResultWithRelationships')
                ? $this->getTheResultWithRelationships($result, $excludeRelationships)
                : $result;
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the records count.
     * @param $filter_vars
     * @return int
     */
    public function getCount($filter_vars) : int
    {
        $sql = "SELECT count(*) AS count FROM $this->model_table $filter_vars->filter_string";
        $result = DB::select($sql, $filter_vars->query_parameters);

        return (int) $result[0]["count"];
    }

    /**
     * Determine if the model has an active property.
     * @return bool
     */
    protected function hasActiveProperty() : bool
    {
        return in_array('active', Schema::getColumnListing($this->model_table));
    }

    /**
     * Determine if the model has a soft deleted property.
     * @return bool
     */
    protected function canBeSoftDeleted() : bool
    {
        return in_array('deleted_at', Schema::getColumnListing($this->model_table));
    }
}
