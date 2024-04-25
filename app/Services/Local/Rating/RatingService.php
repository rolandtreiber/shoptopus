<?php

namespace App\Services\Local\Rating;

use App\Models\Product;
use App\Repositories\Local\Rating\RatingRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;

class RatingService extends ModelService implements RatingServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, RatingRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'rating');
    }

    public function getRatingsForProduct(Product $product, array $ratings, string $languagePrefix, array $page_formatting = [], $filters = []): array
    {
        try {
            return $this->modelRepository->getAllForProduct($product->id, $ratings, $languagePrefix, $page_formatting, $filters);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.product_attribute.getAllForProductCategory'));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.product_attribute.getAllForProductCategory'));
        }
    }
}
