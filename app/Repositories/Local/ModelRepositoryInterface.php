<?php

namespace App\Repositories\Local;

interface ModelRepositoryInterface {

    /**
     * Get all models
     *
     * @param array $page_formatting
     * @param array $filters
     * @param array $excludeRelationships
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []) : array;

    /**
     * Get a single model
     *
     * @param $value
     * @param string $key
     * @param array $excludeRelationships
     * @return array
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []) : array;

    /**
     * Create a model
     *
     * @param array $payload
     * @param bool $returnAsArray
     * @return mixed
     */
    public function post(array $payload, bool $returnAsArray = true);

    /**
     * Update a model
     *
     * @param string $id
     * @param array $payload
     */
    public function update(string $id, array $payload);

    /**
     * Delete a model
     *
     * @param string $id
     */
    public function delete(string $id);

    /**
     * Return models based on the filters and apply page formatting if applicable
     *
     * @param $filter_vars
     * @param array $page_formatting
     * @param array $excludeRelationships
     * @return array
     */
    public function getModels($filter_vars, array $page_formatting = [], array $excludeRelationships = []) : array;

    /**
     * Get the records count
     *
     * @param $filter_vars
     * @return int
     */
    public function getCount($filter_vars) : int;

}
