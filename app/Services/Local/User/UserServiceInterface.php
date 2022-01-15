<?php

namespace App\Services\Local\User;

interface UserServiceInterface {

    /**
     * get the currently authenticated user instance.
     * @param bool $returnAsArray
     * @return null|mixed
     */
    public function getCurrentUser(bool $returnAsArray = true);

    /**
     * get a user model by its email
     * @param string $email
     */
    public function getByEmail(string $email) : array;

    /**
     * get all models
     * @param array $page_formatting
     * @param array $filters
     * @return array
     */
    public function getAll(array $page_formatting = [], array $filters = []) : array;

    /**
     * get a particular model
     * @param int $id
     */
    public function get(int $id);

    /**
     * create a particular model
     * @param array $payload
     */
    public function post(array $payload);

    /**
     * update a particular model
     * @param int $id
     * @param array $payload
     */
    public function update(int $id, array $payload);

    /**
     * delete a particular model
     * @param int $id
     */
    public function delete(int $id);
}
