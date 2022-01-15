<?php

namespace App\Repositories\Local;

interface ModelRepositoryInterface {

    /**
     * get all models
     * @param array $page_formatting
     * @param array $filters
     * @param array $excludeRelationships
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []) : array;

    /**
     * get a single model
     * @param int $id
     * @param array $excludeRelationships
     */
    public function get(int $id, array $excludeRelationships = []);

    /**
     * create a model
     * @param array $payload
     * @param bool $returnAsArray
     * @return mixed
     */
    public function post(array $payload, bool $returnAsArray = true);

    /**
     * update a model
     * @param int $id
     * @param array $payload
     */
    public function update(int $id, array $payload);

    /**
     * delete a model
     * @param int $id
     */
    public function delete(int $id);

    /**
     * Return models based on the filters and apply page formatting if applicable
     * @param $filter_vars
     * @param array $page_formatting
     * @param array $excludeRelationships
     * @return array
     */
    public function getModels($filter_vars, array $page_formatting = [], array $excludeRelationships = []) : array;

    /**
     * Get the records count.
     * @param $filter_vars
     * @return int
     */
    public function getCount($filter_vars) : int;

}
