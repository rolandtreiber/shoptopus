<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Interval;
use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
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

    public function __construct(VoucherCodeRepositoryInterface $voucherCodeRepository)
    {
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return VoucherCodeListResource::collection(VoucherCode::filtered([], $request)->view($request->view)->paginate($request->paginate));
    }

    public function show(VoucherCode $voucherCode): VoucherCodeDetailResource
    {
        return new VoucherCodeDetailResource($voucherCode);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(VoucherCodeStoreRequest $request): VoucherCodeListResource
    {
        $data = $this->getProcessed($request, [], []);
        $voucherCode = new VoucherCode();
        $voucherCode->fill($data);
        $voucherCode->save();

        return new VoucherCodeListResource($voucherCode);
    }

    /**
     * Update a resource.
     */
    public function update(VoucherCode $voucherCode, VoucherCodeUpdateRequest $request): VoucherCodeListResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        $voucherCode->fill($data);
        $voucherCode->save();

        return new VoucherCodeListResource($voucherCode);
    }

    /**
     * @return string[]
     */
    public function delete(VoucherCode $voucherCode): array
    {
        $voucherCode->delete();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
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
     * @return string[]
     *
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
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkActivateForPeriod(VoucherCodeBulkOperationRequest $request): array
    {
        $request->validate([
            'period' => ['required', Rule::in([
                Interval::Day,
                Interval::Week,
                Interval::Month,
            ])],
        ]);
        if ($this->voucherCodeRepository->bulkActivateForPeriod($request->ids, $request->period)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @return string[]
     *
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
