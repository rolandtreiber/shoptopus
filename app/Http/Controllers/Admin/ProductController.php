<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;
use Spatie\Searchable\SearchResultCollection;

class ProductController extends Controller
{

    /**
     * @param ListRequest $request
     */
    public function list(ListRequest $request)
    {
    }
}
