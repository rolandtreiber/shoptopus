<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCategoryStoreRequest;
use App\Http\Requests\Admin\ProductCategoryUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductCategoryDetailResource;
use App\Http\Resources\Admin\ProductCategoryListResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductCategoryController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        if (isset($request->filters["parent_id"])) {
            $dataset = ProductCategory::filtered([], $request);
        } else {
            $dataset = ProductCategory::root()->filtered([], $request);
        }

        return ProductCategoryListResource::collection($dataset->paginate(25));
    }

    /**
     * @param Product $product
     * @param ProductCategory $category
     * @return ProductCategoryDetailResource
     */
    public function show(Product $product, ProductCategory $category): ProductCategoryDetailResource
    {
        return new ProductCategoryDetailResource($category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductCategoryStoreRequest $request
     * @return ProductCategoryDetailResource
     */
    public function create(ProductCategoryStoreRequest $request): ProductCategoryDetailResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        $productCategory = new ProductCategory();
        $productCategory->fill($data);
        $request->hasFile('menu_image') && $productCategory->menu_image = $this->saveFileAndGetUrl($request->menu_image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $request->hasFile('header_image') && $productCategory->header_image = $this->saveFileAndGetUrl($request->header_image, config('shoptopus.header_image_dimensions')[0], config('shoptopus.header_image_dimensions')[1]);
        $productCategory->save();

        return new ProductCategoryDetailResource($productCategory);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductCategoryUpdateRequest $request
     * @param ProductCategory $category
     * @return ProductCategoryDetailResource
     */
    public function update(ProductCategoryUpdateRequest $request, ProductCategory $category): ProductCategoryDetailResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        isset($category->menu_image) && $this->deleteCurrentFile($category->menu_image->file_name);
        isset($category->header_image) && $this->deleteCurrentFile($category->header_image->file_name);
        $category->fill($data);
        $category->menu_image = $request->hasFile('menu_image') ? $this->saveFileAndGetUrl($request->menu_image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]) : null;
        $category->header_image = $request->hasFile('header_image') ? $this->saveFileAndGetUrl($request->header_image, config('shoptopus.header_image_dimensions')[0], config('shoptopus.header_image_dimensions')[1]) : null;
        $category->save();

        return new ProductCategoryDetailResource($category);
    }

    /**
     * @param ProductCategory $category
     * @return string[]
     */
    public function delete(ProductCategory $category): array
    {
        $category->delete();
        return ['status' => 'Success'];
    }
}
