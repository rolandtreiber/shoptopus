<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        return response()->noContent();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Product $product)
    {
        $product->delete();
    }
}
