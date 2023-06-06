<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\PaymentStatusUpdateBulkOperationRequest;
use App\Http\Requests\Admin\PaymentStoreRequest;
use App\Http\Requests\Admin\PaymentUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\PaymentDetailResource;
use App\Http\Resources\Admin\PaymentListResource;
use App\Models\Payment;
use App\Repositories\Admin\Payment\PaymentRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    use ProcessRequest;

    protected PaymentRepositoryInterface $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return PaymentListResource::collection(Payment::filtered([], $request)->view($request->view)->paginate($request->paginate));
    }

    public function show(Payment $payment): PaymentDetailResource
    {
        return new PaymentDetailResource($payment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(PaymentStoreRequest $request): PaymentDetailResource
    {
        $data = $this->getProcessed($request, [], []);
        $payment = new Payment();
        $payment->fill($data);
        $request->hasFile('proof') && $payment->proof = $this->saveFileAndGetUrl($request->proof, config('shoptopus.payment_proof_dimensions')[0], config('shoptopus.payment_proof_dimensions')[1]);
        $payment->save();

        return new PaymentDetailResource($payment);
    }

    /**
     * Update a resource.
     */
    public function update(Payment $payment, PaymentUpdateRequest $request): PaymentDetailResource
    {
        $data = $this->getProcessed($request, [], []);
        isset($payment->proof) && $this->deleteCurrentFile($payment->proof->file_name);
        $payment->fill($data);
        $request->hasFile('proof') && $payment->proof = $this->saveFileAndGetUrl($request->proof, config('shoptopus.payment_proof_dimensions')[0], config('shoptopus.payment_proof_dimensions')[1]);
        $payment->save();

        return new PaymentDetailResource($payment);
    }

    /**
     * @return string[]
     */
    public function delete(Payment $payment): array
    {
        $payment->delete();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkUpdateStatus(PaymentStatusUpdateBulkOperationRequest $request): array
    {
        $request->validate([
            'status' => [
                'required',
                Rule::in(PaymentStatus::getValues()),
            ],
        ]);
        if ($this->paymentRepository->bulkUpdateStatus($request->ids, $request->status)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}
