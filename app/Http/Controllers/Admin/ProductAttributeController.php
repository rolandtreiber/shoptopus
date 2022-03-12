<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\BulkOperationRequest;
use App\Http\Requests\Admin\BulkOperation\ProductAttributeBulkOperationRequest;
use App\Http\Requests\Admin\ProductAttributeStoreRequest;
use App\Http\Requests\Admin\ProductAttributeUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductAttributeDetailResource;
use App\Http\Resources\Admin\ProductAttributeListResource;
use App\Models\ProductAttribute;
use App\Repositories\Admin\ProductAttribute\ProductAttributeRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductAttributeController extends Controller
{
    use ProcessRequest;
    protected ProductAttributeRepositoryInterface $productAttributeRepository;

    /**
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */
    public function __construct(ProductAttributeRepositoryInterface $productAttributeRepository)
    {
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return ProductAttributeListResource::collection(ProductAttribute::filtered([], $request)->availability($request->view)->paginate($request->paginate));
    }

    /**
     * @param ProductAttribute $attribute
     * @return ProductAttributeDetailResource
     */
    public function show(ProductAttribute $attribute): ProductAttributeDetailResource
    {
        return new ProductAttributeDetailResource($attribute);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductAttributeStoreRequest $request
     * @return ProductAttributeDetailResource
     */
    public function create(ProductAttributeStoreRequest $request): ProductAttributeDetailResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        $productAttribute = new ProductAttribute();
        $productAttribute->fill($data);
        $request->hasFile('image') && $productAttribute->image = $this->saveFileAndGetUrl($request->image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $productAttribute->save();

        return new ProductAttributeDetailResource($productAttribute);
    }

    /**
     * Update a resource.
     *
     * @param ProductAttribute $attribute
     * @param ProductAttributeUpdateRequest $request
     * @return ProductAttributeDetailResource
     */
    public function update(ProductAttribute $attribute, ProductAttributeUpdateRequest $request): ProductAttributeDetailResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        isset($attribute->image) && $this->deleteCurrentFile($attribute->image->file_name);
        $attribute->fill($data);
        $attribute->image = $request->hasFile('image') ? $this->saveFileAndGetUrl($request->image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]) : null;
        $attribute->save();

        return new ProductAttributeDetailResource($attribute);
    }

    /**
     * @param ProductAttribute $attribute
     * @return string[]
     */
    public function delete(ProductAttribute $attribute): array
    {
        $attribute->delete();
        return ['status' => 'Success'];
    }

    /**
     * @param ProductAttributeBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkUpdateAvailability(ProductAttributeBulkOperationRequest $request): array
    {
        $request->validate([
            'availability' => ['required', 'boolean']
        ]);
        if ($this->productAttributeRepository->bulkUpdateAvailability($request->ids, $request->availability)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @param ProductAttributeBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkDelete(ProductAttributeBulkOperationRequest $request): array
    {
        if ($this->productAttributeRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}
