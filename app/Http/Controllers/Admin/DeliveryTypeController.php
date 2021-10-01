<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryTypeStoreRequest;
use App\Http\Requests\Admin\DeliveryTypeUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\DeliveryTypeDetailResource;
use App\Http\Resources\Admin\DeliveryTypeListResource;
use App\Models\DeliveryType;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryTypeController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return DeliveryTypeListResource::collection(DeliveryType::filtered([], $request)->paginate(25));
    }

    /**
     * @param DeliveryType $deliveryType
     * @return DeliveryTypeDetailResource
     */
    public function show(DeliveryType $deliveryType): DeliveryTypeDetailResource
    {
        return new DeliveryTypeDetailResource($deliveryType);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DeliveryTypeStoreRequest $request
     * @return DeliveryTypeListResource
     */
    public function create(DeliveryTypeStoreRequest $request): DeliveryTypeListResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        $deliveryType = new DeliveryType();
        $deliveryType->fill($data);
        $deliveryType->save();

        return new DeliveryTypeListResource($deliveryType);
    }

    /**
     * Update a resource.
     *
     * @param DeliveryType $deliveryType
     * @param DeliveryTypeUpdateRequest $request
     * @return DeliveryTypeListResource
     */
    public function update(DeliveryType $deliveryType, DeliveryTypeUpdateRequest $request): DeliveryTypeListResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        $deliveryType->fill($data);
        $deliveryType->save();

        return new DeliveryTypeListResource($deliveryType);
    }

    /**
     * @param DeliveryType $deliveryType
     * @return string[]
     */
    public function delete(DeliveryType $deliveryType): array
    {
        $deliveryType->delete();
        return ['status' => 'Success'];
    }

}
