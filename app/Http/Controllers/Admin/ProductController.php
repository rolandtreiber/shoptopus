<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\ListRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Resources\Admin\ProductDetailResource;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Traits\HasAttributes;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    use ProcessRequest, HasAttributes;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::filtered([
//            ['id', '>=', '9476d4cf-bc20-4585-9d6b-4138bfcbff55'],
//            ['name->en', 'like', '%volupt%']
        ], $request)->paginate(25));
    }

    /**
     * @param Product $product
     * @return ProductDetailResource
     */
    public function show(Product $product): ProductDetailResource
    {
        return new ProductDetailResource($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductStoreRequest $request
     * @return string[]
     */
    public function create(ProductStoreRequest $request): array
    {
        $data = $this->getProcessed($request, [], ['name', 'short_description', 'description']);
        $product = new Product();
        $product->fill($data);
        $product->save();
        $this->saveFiles($request, Product::class, $product->id, true);
        if ($request->product_attributes) {
            $this->handleAttributes($product, $request);
        }

        return ['status' => 'Success'];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return ProductResource
     */
    public function update(ProductUpdateRequest $request, Product $product): ProductResource
    {
        $data = $this->getProcessed($request, [], ['name', 'short_description', 'description']);
        $product->fill($data);
        $product->save();
        $this->saveFiles($request, Product::class, $product->id, true);
        $this->handleAttributes($product, $request);
        return new ProductResource($product);
    }

    /**
     * @param Product $product
     * @return string[]
     */
    public function delete(Product $product): array
    {
        $product->deleteWithAttachments();
        return ['status' => 'Success'];
    }

}
