<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariantStoreRequest;
use App\Http\Requests\Admin\ProductVariantUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\HasAttributes;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductVariantController extends Controller
{
    use ProcessRequest, HasAttributes;

    public function index(ListRequest $request, Product $product): AnonymousResourceCollection
    {
        return ProductVariantResource::collection(ProductVariant::filtered([['product_id', $product->id]], $request)->paginate($request->paginate));
    }

    public function show(Product $product, ProductVariant $variant): ProductVariantResource
    {
        return new ProductVariantResource($variant);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Product $product, ProductVariantStoreRequest $request): ProductVariantResource
    {
        $data = $this->getProcessed($request, [], ['description']);
        $productVariant = new ProductVariant();
        $productVariant->fill($data);
        $productVariant->product_id = $product->id;
        $productVariant->save();
        $this->saveFiles($request, ProductVariant::class, $productVariant->id, true);
        if ($request->product_attributes) {
            $this->handleAttributes($productVariant, $request);
        }
        $productVariant->product->updateAvailableAttributeOptions();


        return new ProductVariantResource($productVariant);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(Product $product, ProductVariant $variant, ProductVariantUpdateRequest $request): ProductVariantResource
    {
        $data = $this->getProcessed($request, [], ['description']);
        $variant->fill($data);
        $variant->save();
        $this->saveFiles($request, ProductVariant::class, $variant->id, true);
        if ($request->product_attributes) {
            $this->handleAttributes($variant, $request);
        }

        return new ProductVariantResource($variant);
    }

    /**
     * @return string[]
     */
    public function delete(Product $product, ProductVariant $variant): array
    {
        $parentProduct = $variant->product;
        $variant->deleteWithAttachments();
        $parentProduct->updateAvailableAttributeOptions();

        return ['status' => 'Success'];
    }
}
