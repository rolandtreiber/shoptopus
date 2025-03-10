<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\DeliveryTypeBulkOperationRequest;
use App\Http\Requests\Admin\DeliveryTypeStoreRequest;
use App\Http\Requests\Admin\DeliveryTypeUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\DeliveryTypeDetailResource;
use App\Http\Resources\Admin\DeliveryTypeListResource;
use App\Models\DeliveryType;
use App\Repositories\Admin\DeliveryType\DeliveryTypeRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryTypeController extends Controller
{
    use ProcessRequest;

    protected DeliveryTypeRepositoryInterface $deliveryTypeRepository;

    public function __construct(DeliveryTypeRepositoryInterface $deliveryTypeRepository)
    {
        $this->deliveryTypeRepository = $deliveryTypeRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return DeliveryTypeListResource::collection(DeliveryType::filtered([], $request)->availability($request->view)->paginate(25));
    }

    public function show(DeliveryType $deliveryType): DeliveryTypeDetailResource
    {
        return new DeliveryTypeDetailResource($deliveryType);
    }

    /**
     * Store a newly created resource in storage.
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
     */
    public function update(DeliveryType $deliveryType, DeliveryTypeUpdateRequest $request): DeliveryTypeListResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        $deliveryType->fill($data);
        $deliveryType->save();

        return new DeliveryTypeListResource($deliveryType);
    }

    /**
     * @return string[]
     */
    public function delete(DeliveryType $deliveryType): array
    {
        $deliveryType->delete();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkUpdateAvailability(DeliveryTypeBulkOperationRequest $request): array
    {
        $request->validate([
            'availability' => ['required', 'boolean'],
        ]);
        if ($this->deliveryTypeRepository->bulkUpdateAvailability($request->ids, $request->availability)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkDelete(DeliveryTypeBulkOperationRequest $request): array
    {
        if ($this->deliveryTypeRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}
