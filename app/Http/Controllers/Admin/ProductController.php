<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\Admin\ProductDetailResource;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Message;
use App\Models\Product;
use App\Traits\ProcessRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::filtered([['id' => 4]])->paginate(25));
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
        $this->saveFiles($request, Product::class, $product->id, false);
        return ['status' => 'Success'];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return string[]
     */
    public function update(ProductUpdateRequest $request, Product $product): array
    {
        $data = $this->getProcessed($request, [], ['name', 'short_description', 'description']);
        $product->fill($data);
        $product->save();
        $this->saveFiles($request, Product::class, $product->id, false);
        return ['status' => 'Success'];
    }
}
