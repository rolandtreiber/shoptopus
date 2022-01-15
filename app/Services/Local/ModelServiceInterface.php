<?php

namespace App\Services\Local;

interface ModelServiceInterface {
    /**
     * get all models
     * @param array $page_formatting
     * @param array $filters
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = []) : array;

    /**
     * get a model
     * @param int $id
     */
    public function get(int $id);

    /**
     * get a model by its slug
     * @param string $slug
     */
    public function getBySlug(string $slug);

    /**
     * get a model by its uuid
     * @param string $uuid
     */
    public function getByUuid(string $uuid);

    /**
     * create a model
     * @param array $payload
     * @param bool $returnAsArray
     * @return mixed
     * @throws \Exception
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
}
