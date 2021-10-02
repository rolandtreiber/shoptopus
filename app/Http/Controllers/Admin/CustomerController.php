<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\CustomerDetailResource;
use App\Http\Resources\Admin\UserListResource;
use App\Models\User;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return UserListResource::collection(User::role('customer')->filtered([], $request)->paginate(25));
    }

    /**
     * @param User $customer
     * @return CustomerDetailResource
     */
    public function show(User $customer): CustomerDetailResource
    {
        $customer->load('cart.products');
        return new CustomerDetailResource($customer);
    }
}
