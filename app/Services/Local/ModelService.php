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
     * Get all models
     *
     *
     * @throws \Exception
     */
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array
    {
        try {
            return $this->modelRepository->getAll($page_formatting, $filters, $excludeRelationships);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.getAll"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.getAll"));
        }
    }

    /**
     * Get a single model
     *
     *
     * @throws \Exception
     */
    public function get($value, string $key = 'id', array $excludeRelationships = []): array
    {
        try {
            return $this->modelRepository->get($value, $key, $excludeRelationships);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        }
    }

    /**
     * Get a single model by its slug
     *
     *
     * @throws \Exception
     */
    public function getBySlug(string $slug): array
    {
        try {
            return $this->modelRepository->get($slug, 'slug');
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get("api_error_codes.services.{$this->modelName}.get"));
        }
    }

    /**
     * Create a model
     *
     * @return mixed
     *
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
     * Update a model
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function update(string $id, array $payload)
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
     * Delete a model
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function delete(string $id)
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
