<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductAttributeStoreRequest;
use App\Http\Requests\Admin\ProductAttributeUpdateRequest;
use App\Http\Requests\Admin\ProductCategoryStoreRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductAttributeDetailResource;
use App\Http\Resources\Admin\ProductAttributeListResource;
use App\Http\Resources\Admin\ProductCategoryDetailResource;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductAttributeController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return ProductAttributeListResource::collection(ProductAttribute::filtered([], $request)->paginate(25));
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
        $request->hasFile('image') && $attribute->image = $this->saveFileAndGetUrl($request->image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
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
}
