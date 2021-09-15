<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Public\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function list(ListRequest $request): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::filtered([['id', '946735b9-00cc-484a-ac60-4c33ba937f31']])->paginate($request->paginate));
    }
}
