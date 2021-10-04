<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Module;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentStoreRequest;
use App\Http\Requests\Admin\PaymentUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\PaymentDetailResource;
use App\Http\Resources\Admin\PaymentListResource;
use App\Models\Payment;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return PaymentListResource::collection(Payment::filtered([], $request)->paginate(25));
    }

    /**
     * @param Payment $payment
     * @return PaymentDetailResource
     */
    public function show(Payment $payment): PaymentDetailResource
    {
        return new PaymentDetailResource($payment);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PaymentStoreRequest $request
     * @return PaymentDetailResource
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
     *
     * @param Payment $payment
     * @param PaymentUpdateRequest $request
     * @return PaymentDetailResource
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
     * @param Payment $payment
     * @return string[]
     */
    public function delete(Payment $payment): array
    {
        $payment->delete();
        return ['status' => 'Success'];
    }
}
