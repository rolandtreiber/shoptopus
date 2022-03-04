<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperationRequest;
use App\Http\Requests\Admin\VoucherCodeStoreRequest;
use App\Http\Requests\Admin\VoucherCodeUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\VoucherCodeDetailResource;
use App\Http\Resources\Admin\VoucherCodeListResource;
use App\Models\VoucherCode;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VoucherCodeController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return VoucherCodeListResource::collection(VoucherCode::filtered([], $request)->view($request->view)->paginate($request->paginate));
    }

    /**
     * @param VoucherCode $voucherCode
     * @return VoucherCodeDetailResource
     */
    public function show(VoucherCode $voucherCode): VoucherCodeDetailResource
    {
        return new VoucherCodeDetailResource($voucherCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VoucherCodeStoreRequest $request
     * @return VoucherCodeListResource
     */
    public function create(VoucherCodeStoreRequest $request): VoucherCodeListResource
    {
        $data = $this->getProcessed($request, [], []);
        $discountRule = new VoucherCode();
        $discountRule->fill($data);
        $discountRule->save();

        return new VoucherCodeListResource($discountRule);
    }

    /**
     * Update a resource.
     *
     * @param VoucherCode $voucherCode
     * @param VoucherCodeUpdateRequest $request
     * @return VoucherCodeListResource
     */
    public function update(VoucherCode $voucherCode, VoucherCodeUpdateRequest $request): VoucherCodeListResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        $voucherCode->fill($data);
        $voucherCode->save();

        return new VoucherCodeListResource($voucherCode);
    }

    /**
     * @param VoucherCode $voucherCode
     * @return string[]
     */
    public function delete(VoucherCode $voucherCode): array
    {
        $voucherCode->delete();
        return ['status' => 'Success'];
    }

    /**
     * @param BulkOperationRequest $request
     * @return string[]
     */
    public function bulkExpire(BulkOperationRequest $request): array
    {
        return ['status' => 'Success'];
    }

    /**
     * @param BulkOperationRequest $request
     * @return string[]
     */
    public function bulkStart(BulkOperationRequest $request): array
    {
        return ['status' => 'Success'];
    }

    /**
     * @param BulkOperationRequest $request
     * @return string[]
     */
    public function bulkActivateForPeriod(BulkOperationRequest $request): array
    {
        return ['status' => 'Success'];
    }

    /**
     * @param BulkOperationRequest $request
     * @return string[]
     */
    public function bulkDelete(BulkOperationRequest $request): array
    {
        return ['status' => 'Success'];
    }
}
