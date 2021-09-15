<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductDetailResource;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{

    /**
     * @param ListRequest $request
     * @return mixed
     */
    public function list(ListRequest $request): mixed
    {
        return ProductResource::collection(Product::filtered([['id' => 4]])->paginate(25));
    }

    /**
     * @param Product $id
     * @return ProductDetailResource
     */
    public function show(Product $id): ProductDetailResource
    {
        return new ProductDetailResource($id);
    }
}
