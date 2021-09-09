<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryRuleStoreRequest;
use Illuminate\Http\Request;

class DeliveryRuleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->noContent();
    }

    /**
     * @param \App\Http\Requests\DeliveryRuleStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryRuleStoreRequest $request)
    {
        
    }
}
