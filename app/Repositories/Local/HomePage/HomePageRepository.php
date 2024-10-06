<?php

namespace App\Repositories\Local\HomePage;

use App\Enums\ProductStatus;
use App\Http\Resources\HomePage\ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HomePageRepository implements HomePageRepositoryInterface
{
    private User|null $user;

    public function getHomePage(User|null $user): array
    {
        $this->user = $user;
        $highlightedProducts = $this->getHighlightedProducts(10);
        return [
            'discounted_categories' => $this->getActiveDiscountsForCategories(3),
            'discounted_products' => $this->getActiveDiscountsForproducts(5),
            'banners' => $this->getBanners(),
            'highlighted_categories' => $this->getHighlightedCategories(3),
            'highlighted_products' => ProductResource::collection(Product::whereIn('id', $highlightedProducts)->get()),
            'new_products' => $this->getNewProducts(5),
        ];
    }

    private function getBanners(): array
    {
        $banners = DB::table('banners')->where('enabled', 1)->select('title', 'description', 'background_image', 'show_button', 'button_text', 'button_url', 'enabled')->get();

        return $banners->map(function ($banner) {
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

    private function getHighlightedProducts($limit): array
    {
        $productIdsWithHighSalesVolume = DB::table('products')
            ->orderBy('purchase_count', 'desc')
            ->select('id')->limit($limit)
            ->pluck('id')
            ->toArray();

        $discountedProductsByCategory = DB::table('discount_rule_product_category')->join('discount_rules', function ($join) {
            $join->on('discount_rule_product_category.discount_rule_id', '=', 'discount_rules.id')->where('discount_rules.enabled', '=', 1);
        })->join('product_product_category', function ($join) {
            $join->on('discount_rule_product_category.product_category_id', '=', 'product_product_category.product_category_id');
        })->join('products', function ($join) {
            $join->on('products.id', '=', 'product_product_category.product_id')
                ->where('products.status', '=', ProductStatus::Active)
                ->where('products.stock', '>', 0);
        })
            ->select('product_id')->limit($limit)->pluck('product_id')->toArray();

        $discountedProductsDirectly = DB::table('discount_rule_product')->join('discount_rules', function ($join) {
            $join->on('discount_rule_product.discount_rule_id', '=', 'discount_rules.id')
                ->where(function ($query) {
                    $query->where('discount_rules.valid_from', '<', Carbon::now())
                        ->where('discount_rules.valid_until', '>', Carbon::now());
                })->orWhere(function ($query) {
                    $query->where('discount_rules.valid_from', '<', Carbon::now())
                        ->where('discount_rules.valid_until', null);
                })->where('discount_rules.enabled', '=', 1);
        })->join('products', function ($join) {
            $join->on('products.id', '=', 'discount_rule_product.product_id')
                ->where('products.status', '=', ProductStatus::Active)
                ->where('products.stock', '>', 0);
        })->select('discount_rule_product.product_id')->limit($limit)->pluck('product_id')->toArray();

        $likedProducts = [];
        if ($this->user) {
            $likedProducts = DB::table('favorited_products')->where('user_id', $this->user->id)
                ->join('products', function ($join) {
                    $join->on('products.id', '=', 'favorited_products.product_id')
                        ->where('products.status', '=', ProductStatus::Active)
                        ->where('products.stock', '>', 0);
                })->pluck('product_id')->toArray();
        }

        $allDisplayable = array_merge($productIdsWithHighSalesVolume, $discountedProductsByCategory, $discountedProductsDirectly, $likedProducts);
        $total = count($allDisplayable);
        if ($total < $limit) {
            $totalRemainingProductsCount = DB::table('products')->where('status', ProductStatus::Active)->count() - $total;
            if ($totalRemainingProductsCount > 25 - $total) {
                $remainingProductIds = DB::table('products')->where('status', ProductStatus::Active)->whereNotIn('id', $allDisplayable)->pluck('id')->toArray();
                shuffle($remainingProductIds);
                $allDisplayable = array_merge($allDisplayable, array_slice($remainingProductIds, 0, $limit - $total));
            }
        }

        shuffle($allDisplayable);

        return array_slice($allDisplayable, 0, $limit);
    }

    private function getHighlightedCategories(int $limit)
    {
        $highlightedCategoryIds = DB::table('discount_rule_product_category')
            ->join('discount_rules', function ($join) {
                $join->on('discount_rule_product_category.discount_rule_id', '=', 'discount_rules.id')
                    ->where(function ($query) {
                        $query->where('discount_rules.valid_from', '<', Carbon::now())
                            ->where('discount_rules.valid_until', '>', Carbon::now());
                    })->orWhere(function ($query) {
                        $query->where('discount_rules.valid_from', '<', Carbon::now())
                            ->where('discount_rules.valid_until', null);
                    });
            })
            ->where('enabled', 1)
            ->pluck('product_category_id')->toArray();

        $missing = $limit - count($highlightedCategoryIds);
        if ($missing > 0) {
            $remainingCategoryIds = DB::table('product_categories')
                ->where('enabled', 1)
                ->whereNotIn('id', $highlightedCategoryIds)
                ->pluck('id')
                ->toArray();
            shuffle($remainingCategoryIds);
            $highlightedCategoryIds = array_merge($highlightedCategoryIds, array_slice($remainingCategoryIds, 0, $missing));
        }

        $result = [];

        $items = DB::table('product_categories')
            ->whereIn('product_categories.id', $highlightedCategoryIds)
            ->join('product_product_category', function ($join) {
                $join->on('product_product_category.product_category_id', '=', 'product_categories.id');
            })
            ->join('products', function ($join) {
                $join->on('products.id', '=', 'product_product_category.product_id')
                    ->where('products.status', ProductStatus::Active)
                    ->where('products.stock', '>', 0)
                    ->orderBy('products.purchase_count', 'desc');
            })
            ->get([
                'product_categories.name as category_name',
                'product_categories.description as category_description',
                'product_categories.header_image as category_header_image',
                'product_categories.id as category_id',
                'products.id as bestseller_id',
                'products.name as product_name',
                'products.slug as product_slug',
                'products.price as product_price',
                'products.stock as product_stock',
                'products.purchase_count as product_purchase_count',
                'products.short_description as product_short_description',
                'products.description as product_description',
                'products.cover_photo as product_cover_photo',
            ]);

        $count = 0;
        foreach ($items as $item) {
            if ($count <= $limit) {
                if (array_key_exists($item['category_id'], $result) && count($result[$item['category_id']]['best_sellers']) < 3) {
                    $result[$item['category_id']]['best_sellers'][] = [
                        'id' => $item['bestseller_id'],
                        'name' => json_decode($item['product_name']),
                        'slug' => $item['product_slug'],
                        'price' => $item['product_price'],
                        'stock' => $item['product_stock'],
                        'purchase_count' => $item['product_purchase_count'],
                        'short_description' => json_decode($item['product_short_description']),
                        'description' => json_decode($item['product_description']),
                        'cover_photo' => json_decode($item['product_cover_photo'])
                    ];
                } else {
                    $count++;
                    $result[$item['category_id']] = [
                        'name' => json_decode($item['category_name']),
                        'description' => json_decode($item['category_description']),
                        'header_image' => json_decode($item['category_header_image']),
                        'best_sellers' => [
                            [
                                'id' => $item['bestseller_id'],
                                'name' => json_decode($item['product_name']),
                                'slug' => $item['product_slug'],
                                'price' => $item['product_price'],
                                'stock' => $item['product_stock'],
                                'purchase_count' => $item['product_purchase_count'],
                                'short_description' => json_decode($item['product_short_description']),
                                'description' => json_decode($item['product_description']),
                                'cover_photo' => json_decode($item['product_cover_photo'])
                            ]
                        ],
                    ];
                }
            }
        }

        $arrayKeys = array_keys($result);
        $resultFormatted = [];
        foreach ($arrayKeys as $arrayKey) {
            $resultFormatted[] = [
                'id' => $arrayKey,
                ...$result[$arrayKey]
            ];
        }
        return $resultFormatted;
    }

    private function getNewProducts(int $limit): Collection
    {
        return Product::where('status', ProductStatus::Active)->where('stock', '>', 0)->limit($limit)->orderBy('created_at', 'desc')->get();
    }

    /**
     * @param int $limit
     */
    private function getActiveDiscountsForCategories(int $limit)
    {
        $activeDiscounts = DB::table('discount_rule_product_category')
        ->join('discount_rules', function ($join) {
            $join->on('discount_rule_product_category.discount_rule_id', '=', 'discount_rules.id')
                ->where('discount_rules.valid_from', '<', Carbon::now())
                ->where('discount_rules.valid_until', '>', Carbon::now())
                ->where('discount_rules.enabled', 1);
        })->join('product_categories', function ($join) {
            $join->on('product_categories.id', '=', 'discount_rule_product_category.product_category_id');
            })
            ->limit($limit)->get([
                'discount_rules.id as discount_rule_id',
                'discount_rules.name as discount_rule_name',
                'valid_from',
                'valid_until',
                'type',
                'amount',
                'product_categories.id as product_category_id',
                'product_categories.name as product_category_name',
                'product_categories.header_image as product_category_header_image',
            ]);

        return $activeDiscounts->map(function ($discount) {
            return [
                'id' => $discount['discount_rule_id'],
                'name' => json_decode($discount['discount_rule_name']),
                'valid_from' => $discount['valid_from'],
                'valid_until' => $discount['valid_until'],
                'type' => $discount['type'],
                'amount' => $discount['amount'],
                'category_id' => $discount['product_category_id'],
                'category_header_image' => json_decode($discount['product_category_header_image']),
            ];
        });
    }

    /**
     * @param int $limit
     */
    private function getActiveDiscountsForProducts(int $limit)
    {
        $activeDiscounts = DB::table('discount_rule_product')
            ->join('discount_rules', function ($join) {
                $join->on('discount_rule_product.discount_rule_id', '=', 'discount_rules.id')
                    ->where('discount_rules.valid_from', '<', Carbon::now())
                    ->where('discount_rules.valid_until', '>', Carbon::now())
                    ->where('discount_rules.enabled', 1);
            })->join('products', function ($join) {
                $join->on('products.id', '=', 'discount_rule_product.product_id');
            })
            ->limit($limit)->get([
                'discount_rules.id as discount_rule_id',
                'discount_rules.name as discount_rule_name',
                'valid_from',
                'valid_until',
                'type',
                'amount',
                'products.id as product_id',
                'products.name as product_name',
                'products.stock as product_stock',
                'products.cover_photo as product_cover_photo',
            ]);

        return $activeDiscounts->map(function ($discount) {
            return [
                'id' => $discount['discount_rule_id'],
                'name' => json_decode($discount['discount_rule_name']),
                'valid_from' => $discount['valid_from'],
                'valid_until' => $discount['valid_until'],
                'type' => $discount['type'],
                'amount' => $discount['amount'],
                'product_id' => $discount['product_id'],
                'product_stock' => $discount['product_stock'],
                'product_cover_photo' => json_decode($discount['product_cover_photo']),
            ];
        });
    }

}
