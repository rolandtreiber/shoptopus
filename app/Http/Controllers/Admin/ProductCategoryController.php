<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\ProductCategoryBulkOperationRequest;
use App\Http\Requests\Admin\ProductCategoryStoreRequest;
use App\Http\Requests\Admin\ProductCategoryUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductCategoryDetailResource;
use App\Http\Resources\Admin\ProductCategoryListResource;
use App\Http\Resources\Admin\ProductCategorySelectResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\Admin\ProductCategory\ProductCategoryRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductCategoryController extends Controller
{
    use ProcessRequest;

    protected ProductCategoryRepositoryInterface $productCategoryRepository;

    /**
     * @param  ProductCategoryRepositoryInterface  $productCategoryRepository
     */
    public function __construct(ProductCategoryRepositoryInterface $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    /**
     * @param  ListRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        if (isset($request->filters['parent_id'])) {
            $dataset = ProductCategory::filtered([], $request);
        } else {
            if ($request->filters) {
                $dataset = ProductCategory::filtered([], $request);
            } else {
                $dataset = ProductCategory::root()->filtered([], $request);
            }
        }

        return ProductCategoryListResource::collection($dataset->availability($request->view)->paginate($request->paginate));
    }

    /**
     * @param  Product  $product
     * @param  ProductCategory  $category
     * @return ProductCategoryDetailResource
     */
    public function show(Product $product, ProductCategory $category): ProductCategoryDetailResource
    {
        return new ProductCategoryDetailResource($category);
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getSelectData(): AnonymousResourceCollection
    {
        return ProductCategorySelectResource::collection(ProductCategory::where('enabled', 1)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProductCategoryStoreRequest  $request
     * @return ProductCategoryDetailResource
     */
    public function create(ProductCategoryStoreRequest $request): ProductCategoryDetailResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        unset($data['header_image']);
        unset($data['menu_image']);
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
     * @param  ProductCategoryUpdateRequest  $request
     * @param  ProductCategory  $category
     * @return ProductCategoryDetailResource
     */
    public function update(ProductCategoryUpdateRequest $request, ProductCategory $category): ProductCategoryDetailResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        isset($category->menu_image) && $this->deleteCurrentFile($category->menu_image->file_name);
        isset($category->header_image) && $this->deleteCurrentFile($category->header_image->file_name);
        unset($data['header_image']);
        unset($data['menu_image']);
        $category->fill($data);
        $category->menu_image = $request->hasFile('menu_image') ? $this->saveFileAndGetUrl($request->menu_image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]) : null;
        $category->header_image = $request->hasFile('header_image') ? $this->saveFileAndGetUrl($request->header_image, config('shoptopus.header_image_dimensions')[0], config('shoptopus.header_image_dimensions')[1]) : null;
        $category->save();

        return new ProductCategoryDetailResource($category);
    }

    /**
     * @param  ProductCategory  $category
     * @return string[]
     */
    public function delete(ProductCategory $category): array
    {
        $category->delete();

        return ['status' => 'Success'];
    }

    /**
     * @param  ProductCategoryBulkOperationRequest  $request
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkUpdateAvailability(ProductCategoryBulkOperationRequest $request): array
    {
        $request->validate([
            'availability' => ['required', 'boolean'],
        ]);
        if ($this->productCategoryRepository->bulkUpdateAvailability($request->ids, $request->availability)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @param  ProductCategoryBulkOperationRequest  $request
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkDelete(ProductCategoryBulkOperationRequest $request): array
    {
        if ($this->productCategoryRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}
