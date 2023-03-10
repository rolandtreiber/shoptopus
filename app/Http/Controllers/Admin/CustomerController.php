<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\CustomerDetailResource;
use App\Http\Resources\Admin\CustomerListResource;
use App\Models\User;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    use ProcessRequest;

    /**
     * @param  ListRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return CustomerListResource::collection(User::customers()->view($request->view)->filtered([], $request)->paginate($request->paginate));
    }

    /**
     * @param  User  $customer
     * @return CustomerDetailResource
     */
    public function show(User $customer): CustomerDetailResource
    {
        $customer->load('cart.products');

        return new CustomerDetailResource($customer);
    }
}
