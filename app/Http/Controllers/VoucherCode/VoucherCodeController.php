<?php

namespace App\Http\Controllers\VoucherCode;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoucherCode\PostRequest;
use App\Http\Requests\VoucherCode\PatchRequest;
use App\Services\Local\VoucherCode\VoucherCodeServiceInterface;

class VoucherCodeController extends Controller
{
    private VoucherCodeServiceInterface $voucherCodeService;

    public function __construct(VoucherCodeServiceInterface $voucherCodeService)
    {
        $this->voucherCodeService = $voucherCodeService;
    }

    /**
     * Get all models
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request) : \Illuminate\Http\JsonResponse
    {
        try {
            $filters = $this->getAndValidateFilters($request);
            $filters['deleted_at'] = $filters['deleted_at'] ?? 'null';
            $page_formatting = $this->getPageFormatting($request);
            return response()->json($this->getResponse($page_formatting, $this->voucherCodeService->getAll($page_formatting, $filters), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Get a single model
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, string $id) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->voucherCodeService->get($id), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Create a model
     *
     * @param PostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(PostRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->voucherCodeService->post($request->validated());
            return response()->json($this->postResponse($data));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Update a model
     *
     * @param PatchRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PatchRequest $request, string $id) : \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->voucherCodeService->update($id, $request->validated());
            return response()->json($this->putResponse($data));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Delete a model
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(string $id) : \Illuminate\Http\JsonResponse
    {
        try {
            $this->voucherCodeService->delete($id);
            return response()->json($this->deleteResponse());
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }
}
