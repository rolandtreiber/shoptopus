<?php

namespace App\Services\Local;

interface ModelServiceInterface {

    /**
     * Get all models
     *
     * @param array $page_formatting
     * @param array $filters
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = []) : array;

    /**
     * Get a single model
     *
     * @param string $id
     * @param array $excludeRelationships
     * @return mixed
     * @throws \Exception
     */
    public function get(string $id, array $excludeRelationships = []) : array;

    /**
     * Get a model by its slug
     *
     * @param string $slug
     */
    public function getBySlug(string $slug);

    /**
     * Create a model
     *
     * @param array $payload
     * @param bool $returnAsArray
     * @return mixed
     * @throws \Exception
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
}
