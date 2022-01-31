<?php

namespace App\Services\Local\Address;

interface AddressServiceInterface {

    /**
     * get all models
     * @param array $page_formatting
     * @param array $filters
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = []) : array;

    /**
     * get a particular model
     * @param string $id
     */
    public function get(string $id);

    /**
     * create a particular model
     * @param array $payload
     */
    public function post(array $payload);

    /**
     * update a particular model
     * @param string $id
     * @param array $payload
     */
    public function update(string $id, array $payload);

    /**
     * delete a particular model
     * @param string $id
     */
    public function delete(string $id);
}
