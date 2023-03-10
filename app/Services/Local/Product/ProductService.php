<?php

namespace App\Services\Local\Product;

use App\Repositories\Local\Product\ProductRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;

class ProductService extends ModelService implements ProductServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'product');
    }

    /**
     * Save product to favorites
     *
     * @param  string  $productId
     * @return array
     *
     * @throws \Exception
     */
    public function favorite(string $productId): array
    {
        try {
            return $this->modelRepository->favorite($productId);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.product.favorite'));
        }
    }
}
