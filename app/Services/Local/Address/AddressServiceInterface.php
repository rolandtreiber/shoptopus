<?php

namespace App\Services\Local\Address;

use Exception;

interface AddressServiceInterface
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
     *
     * @throws Exception
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
}
