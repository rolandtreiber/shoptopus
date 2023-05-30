<?php

namespace App\Repositories\Local;

interface ModelRepositoryInterface
{
    /**
     * Get all models
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

    /**
     * Get a single model
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

    /**
     * Create a model
     *
     * @return mixed
     */
    public function post(array $payload, bool $returnAsArray = true);

    /**
     * Update a model
     */
    public function update(string $id, array $payload);

    /**
     * Delete a model
     */
    public function delete(string $id);

    /**
     * Return models based on the filters and apply page formatting if applicable
     */
    public function getModels($filter_vars, array $page_formatting = [], array $excludeRelationships = []): array;

    /**
     * Get the records count
     */
    public function getCount($filter_vars): int;
}
