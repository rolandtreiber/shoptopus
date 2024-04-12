<?php

namespace App\Services\Local\Product;

use App\Enums\AccessTokenType;
use App\Exceptions\InvalidAccessTokenException;
use App\Exceptions\ProductIsNotInOrderException;
use App\Models\AccessToken;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Rating;
use App\Models\User;
use App\Repositories\Local\Product\ProductRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Nested;

class ProductService extends ModelService implements ProductServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'product');
    }

    /**
     * Save product to favorites
     *
     *
     * @throws Exception
     */
    public function favorite(string $productId): array
    {
        try {
            return $this->modelRepository->favorite($productId);
        } catch (Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new Exception($e->getMessage(), Config::get('api_error_codes.services.product.favorite'));
        }
    }

    /**
     * @throws InvalidAccessTokenException
     * @throws Exception
     */
    public function saveReview(string $productId, array $data): array
    {
        try {
            /** @var AccessToken $token */
            $token = AccessToken::where('token', $data['token'])->first();
            // @phpstan-ignore-next-line
            if (!$token || $token->type !== AccessTokenType::Review) {
                throw new InvalidAccessTokenException();
            }
            $user = $token->issuer;
            /** @var Order $order */
            $order = $token->accessable;
            $orderProduct = OrderProduct::where([
                'order_id' => $order->id,
                'product_id' => $productId
                ])->first();
            if (!$orderProduct) {
                throw new ProductIsNotInOrderException();
            }
            $review = Rating::where([
                'user_id' => $user->id,
                'ratable_id' => $productId,
                'ratable_type' => Product::class
                ])->first();
            if (!$review) {
                $review = new Rating();
                $review->ratable_id = $productId;
                $review->ratable_type = Product::class;
                $review->user_id = $user->id;
            }
            if (array_key_exists('language_prefix', $data)) {
                $review->language_prefix = $data['language_prefix'];
            } else {
                $review->language_prefix = array_keys(config('app.locales_supported'))[0];
            }
            $review->title = $data['title'];
            $review->description = $data['description'];
            $review->verified = true;
            $review->rating = $data['rating'];
            $review->save();
            return [
                'message' => 'Review saved',
                'id' => $review->id
            ];
        } catch (Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new Exception($e->getMessage(), Config::get('api_error_codes.services.product.review'));
        }
    }

    public function search(string $search): Collection
    {
        return Product::search($search)->get();
    }
}
