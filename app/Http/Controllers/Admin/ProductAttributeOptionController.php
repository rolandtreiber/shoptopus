<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\OptionDoesNotBelongToAttributeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductAttributeOptionStoreRequest;
use App\Http\Requests\Admin\ProductAttributeOptionUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductAttributeOptionDetailResource;
use App\Http\Resources\Admin\ProductAttributeOptionListResource;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductAttributeOptionController extends Controller
{
    use ProcessRequest;

    public function index(ProductAttribute $attribute, ListRequest $request): AnonymousResourceCollection
    {
        return ProductAttributeOptionListResource::collection(ProductAttributeOption::filtered([['product_attribute_id', $attribute->id]], $request)->paginate(25));
    }

    /**
     * @throws OptionDoesNotBelongToAttributeException
     */
    public function show(ProductAttribute $attribute, ProductAttributeOption $option): ProductAttributeOptionDetailResource
    {
        if (! $attribute->options->contains($option)) {
            throw new OptionDoesNotBelongToAttributeException();
        }

        return new ProductAttributeOptionDetailResource($option);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(ProductAttribute $attribute, ProductAttributeOptionStoreRequest $request): ProductAttributeOptionDetailResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        unset($data['image']);
        $productAttributeOption = new ProductAttributeOption();
        $productAttributeOption->fill($data);
        $productAttributeOption->product_attribute_id = $attribute->id;
        $request->hasFile('image') && $productAttributeOption->image = $this->saveFileAndGetUrl($request->image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $productAttributeOption->save();

        return new ProductAttributeOptionDetailResource($productAttributeOption);
    }

    /**
     * Update a resource.
     *
     *
     * @throws OptionDoesNotBelongToAttributeException
     */
    public function update(ProductAttribute $attribute, ProductAttributeOption $option, ProductAttributeOptionUpdateRequest $request): ProductAttributeOptionDetailResource
    {
        if (! $attribute->options->contains($option)) {
            throw new OptionDoesNotBelongToAttributeException();
        }
        $data = $this->getProcessed($request, [], ['name']);
        unset($data['image']);
        isset($option->image) && $this->deleteCurrentFile($option->image->file_name);
        $option->fill($data);
        $request->hasFile('image') && $option->image = $this->saveFileAndGetUrl($request->image, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $option->save();

        return new ProductAttributeOptionDetailResource($option);
    }

    /**
     * @return string[]
     *
     * @throws OptionDoesNotBelongToAttributeException
     */
    public function delete(ProductAttribute $attribute, ProductAttributeOption $option): array
    {
        if (! $attribute->options->contains($option)) {
            throw new OptionDoesNotBelongToAttributeException();
        }
        $option->delete();

        return ['status' => 'Success'];
    }
}
