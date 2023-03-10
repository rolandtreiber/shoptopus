<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\BulkOrderStatusUpdateRequest;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\OrderDetailResource;
use App\Http\Resources\Admin\OrderListResource;
use App\Models\Order;
use App\Repositories\Admin\Order\OrderRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    use ProcessRequest;

    protected OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param  ListRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return OrderListResource::collection(Order::filtered([], $request)->search($request->search)->view($request->view)->join('users as user', 'orders.user_id', '=', 'user.id')->select('orders.*')->paginate($request->paginate));
    }

    /**
     * Display the specified resource.
     *
     * @param  Order  $order
     * @return OrderDetailResource
     */
    public function show(Order $order): OrderDetailResource
    {
        return new OrderDetailResource($order->load('payments.payment_source'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  OrderStatusUpdateRequest  $request
     * @param  Order  $order
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
     * @param  Order  $order
     * @return string[]
     */
    public function delete(Order $order): array
    {
        $order->delete();

        return ['status' => 'Success'];
    }

    /**
     * @param  BulkOrderStatusUpdateRequest  $request
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkStatusUpdate(BulkOrderStatusUpdateRequest $request): array
    {
        if ($this->orderRepository->bulkUpdateStatus($request->ids, $request->status)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}
