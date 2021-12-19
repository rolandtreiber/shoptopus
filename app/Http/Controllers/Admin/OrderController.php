<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\OrderDetailResource;
use App\Http\Resources\Admin\OrderListResource;
use App\Models\Order;
use App\Traits\ProcessRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return OrderListResource::collection(Order::filtered([], $request)->search($request->search)->view($request->view)->paginate($request->paginate));
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return OrderDetailResource
     */
    public function show(Order $order): OrderDetailResource
    {
        return new OrderDetailResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrderStatusUpdateRequest $request
     * @param Order $order
     * @return OrderListResource
     */
    public function updateStatus(OrderStatusUpdateRequest $request, Order $order): OrderListResource
    {
        $order->status = $request->status;
        $order->save();

        return new OrderListResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     * @return string[]
     */
    public function delete(Order $order): array
    {
        $order->delete();
        return ['status' => 'Success'];
    }
}
