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
        return ProductResource::collection(Product::filtered([['id', 4]])->paginate($request->paginate));
    }
}
