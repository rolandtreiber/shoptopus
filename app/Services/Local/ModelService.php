<?php

namespace App\Services\Local;

use Illuminate\Support\Facades\Config;

class ModelService implements ModelServiceInterface
{
    protected $modelRepository;
    protected $errorService;
    protected $modelName;

    public function __construct($errorService, $modelRepository, $modelName)
    {
        $this->errorService = $errorService;
        $this->modelRepository = $modelRepository;
        $this->modelName = $modelName;
    }

    /**
     * get all models
     * @param array $page_formatting
     * @param array $filters
     * @return array
     * @throws \Exception
     */
    public function getAll(array $page_formatting = [], array $filters = []) : array
    {
        try {
            return $this->modelRepository->getAll($page_formatting, $filters);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.getAll"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.getAll"));
        }
    }

    /**
     * get a single model
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function get(int $id)
    {
        try {
            return $this->modelRepository->get($id);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        }
    }

    /**
     * get a single model by its slug
     * @param string $slug
     * @return mixed
     * @throws \Exception
     */
    public function getBySlug(string $slug)
    {
        try {
            return $this->modelRepository->getBySlug($slug);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        }
    }

    /**
     * get a single model by its slug
     * @param string $uuid
     * @return mixed
     * @throws \Exception
     */
    public function getByUuid(string $uuid)
    {
        try {
            return $this->modelRepository->getByUuid($uuid);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        }
    }

    /**
     * create a model
     * @param array $payload
     * @param bool $returnAsArray
     * @return mixed
     * @throws \Exception
     */
    public function post(array $payload, bool $returnAsArray = true)
    {
        try {
            return $this->modelRepository->post($payload, $returnAsArray);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.post"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.post"));
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
            return $this->modelRepository->update($id, $payload);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.update"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.update"));
        }
    }

    /**
     * delete a model
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function delete(int $id)
    {
        try {
            return $this->modelRepository->delete($id);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.delete"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.delete"));
        }
    }
}
