<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Admin\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\HasAttributes;
use App\Traits\ProcessRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductVariantController extends Controller
{
    use ProcessRequest, HasAttributes;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request, Product $product): AnonymousResourceCollection
    {
        return ProductVariantResource::collection(ProductVariant::filtered([['product_id', $product->id]], $request)->paginate(25));
    }
}
