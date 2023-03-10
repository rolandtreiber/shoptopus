<?php

namespace App\Services\Local;

interface ModelServiceInterface
{
    /**
     * Get all models
     *
     * @param  array  $page_formatting
     * @param  array  $filters
     * @param  array  $excludeRelationships
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

    /**
     * Get a single model
     *
     * @param $value
     * @param  string  $key
     * @param  array  $excludeRelationships
     * @return array
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

    /**
     * Get a single model by its slug
     *
     * @param  string  $slug
     * @return array
     *
     * @throws \Exception
     */
    public function getBySlug(string $slug): array;

    /**
     * Create a model
     *
     * @param  array  $payload
     * @param  bool  $returnAsArray
     * @return mixed
     *
     * @throws \Exception
     */
    public function post(array $payload, bool $returnAsArray = true);

    /**
     * Update a model
     *
     * @param  string  $id
     * @param  array  $payload
     */
    public function update(string $id, array $payload);

    /**
     * Delete a model
     *
     * @param  string  $id
     */
    public function delete(string $id);
}
