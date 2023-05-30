<?php

namespace App\Services\Local\ProductAttribute;

use App\Repositories\Local\ProductAttribute\ProductAttributeRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;

class ProductAttributeService extends ModelService implements ProductAttributeServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductAttributeRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'product_attribute');
    }

    /**
     * Get all models for a specific product category
     *
     *
     * @throws \Exception
     */
    public function getAllForProductCategory(string $product_category_id, array $page_formatting = []): array
    {
        try {
            return $this->modelRepository->getAllForProductCategory($product_category_id, $page_formatting);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.product_attribute.getAllForProductCategory'));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.product_attribute.getAllForProductCategory'));
        }
    }
}
