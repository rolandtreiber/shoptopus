<?php

namespace App\Repositories\Local\HomePage;

use App\Enums\DiscountType;
use App\Enums\ProductStatus;
use App\Http\Resources\HomePage\ProductResource;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Local\Product\ProductRepositoryInterface;
use App\Services\Local\Product\ProductServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomePageRepository implements HomePageRepositoryInterface
{
    private User $user;

    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getHomePage(User|null $user): array
    {
        $this->user = $user;
        $highlightedProducts = $this->getHighlightedProducts();
        return [
            'banners' => $this->getBanners(),
            'highlighted_products' => ProductResource::collection(Product::whereIn('id', $highlightedProducts)->get()),
        ];
    }

    private function getBanners(): array
    {
        $banners = DB::table('banners')->where('enabled', 1)->select('title', 'description', 'background_image', 'show_button', 'button_text', 'button_url', 'enabled')->get();

        return $banners->map(function($banner) {
            return [
                'title' => json_decode($banner['title']),
                'description' => json_decode($banner['description']),
                'background_image' => json_decode($banner['background_image']),
                'show_button' => $banner['show_button'],
                'button_text' => json_decode($banner['button_text']),
                'button_url' => $banner['button_url'],
                'enabled' => $banner['enabled']
            ];
        })->toArray();
    }

    private function getHighlightedProducts()
    {
        $productIdsWithHighSalesVolume = DB::table('products')
            ->orderBy('purchase_count', 'desc')
            ->select('id')->limit(20)
            ->pluck('id')
            ->toArray();

        $discountedProductsByCategory = DB::table('discount_rule_product_category')->join('discount_rules', function ($join) {
            $join->on('discount_rule_product_category.discount_rule_id', '=', 'discount_rules.id')->where('discount_rules.enabled', '=', 1);
        })->join('product_product_category', function($join) {
            $join->on('discount_rule_product_category.product_category_id', '=', 'product_product_category.product_category_id');
        })->join('products', function ($join) {
            $join->on('products.id', '=', 'product_product_category.product_id')
                ->where('products.status', '=', ProductStatus::Active)
                ->where('products.stock', '>', 0)
            ;
        })
            ->select('product_id')->limit(50)->pluck('product_id')->toArray();

        $discountedProductsDirectly = DB::table('discount_rule_product')->join('discount_rules', function ($join) {
            $join->on('discount_rule_product.discount_rule_id', '=', 'discount_rules.id')->where('discount_rules.enabled', '=', 1);
        })->join('products', function ($join) {
            $join->on('products.id', '=', 'discount_rule_product.product_id')
                ->where('products.status', '=', ProductStatus::Active)
                ->where('products.stock', '>', 0)
            ;
        })->select('discount_rule_product.product_id')->limit(50)->pluck('product_id')->toArray();

        $likedProducts = [];
        if ($this->user) {
            $likedProducts = DB::table('favorited_products')->where('user_id', $this->user->id)
                ->join('products', function ($join) {
                $join->on('products.id', '=', 'favorited_products.product_id')
                    ->where('products.status', '=', ProductStatus::Active)
                    ->where('products.stock', '>', 0)
                ;
            })->pluck('product_id')->toArray();
        }

        $allDisplayable = array_merge($productIdsWithHighSalesVolume, $discountedProductsByCategory, $discountedProductsDirectly, $likedProducts);
        $total = count($allDisplayable);
        if ($total < 25) {
            $totalRemainingProductsCount = DB::table('products')->where('status', ProductStatus::Active)->count() - $total;
            if ($totalRemainingProductsCount > 25 - $total) {
                $remainingProductIds = DB::table('products')->where('status', ProductStatus::Active)->whereNotIn('id', $allDisplayable)->pluck('id')->toArray();
                shuffle($remainingProductIds);
                $allDisplayable = array_merge($allDisplayable, array_slice($remainingProductIds, 0, 25 - $total));
            }
        }

        shuffle($allDisplayable);
        return $allDisplayable;
    }
}