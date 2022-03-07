<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Intervals;
use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\BulkOperationRequest;
use App\Http\Requests\Admin\BulkOperation\VoucherCodeBulkOperationRequest;
use App\Http\Requests\Admin\VoucherCodeStoreRequest;
use App\Http\Requests\Admin\VoucherCodeUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\VoucherCodeDetailResource;
use App\Http\Resources\Admin\VoucherCodeListResource;
use App\Models\VoucherCode;
use App\Repositories\Admin\VoucherCode\VoucherCodeRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class VoucherCodeController extends Controller
{
    use ProcessRequest;
    protected VoucherCodeRepositoryInterface $voucherCodeRepository;

    /**
     * @param VoucherCodeRepositoryInterface $voucherCodeRepository
     */
    public function __construct(VoucherCodeRepositoryInterface $voucherCodeRepository)
    {
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

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
     * @param VoucherCodeBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkExpire(VoucherCodeBulkOperationRequest $request): array
    {
        if ($this->voucherCodeRepository->bulkExpire($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();

    }

    /**
     * @param VoucherCodeBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkStart(VoucherCodeBulkOperationRequest $request): array
    {
        if ($this->voucherCodeRepository->bulkStart($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();

    }

    /**
     * @param VoucherCodeBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkActivateForPeriod(VoucherCodeBulkOperationRequest $request): array
    {
        $request->validate([
            'period' => ['required', Rule::in([
                Intervals::Day,
                Intervals::Week,
                Intervals::Month
            ])]
        ]);
        if ($this->voucherCodeRepository->bulkActivateForPeriod($request->ids, $request->period)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @param VoucherCodeBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkDelete(VoucherCodeBulkOperationRequest $request): array
    {
        if ($this->voucherCodeRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}
