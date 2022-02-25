<?php

namespace App\Http\Controllers\Address;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\PostRequest;
use App\Http\Requests\Address\PatchRequest;
use App\Services\Local\User\UserServiceInterface;
use App\Http\Requests\Address\DeleteAddressRequest;
use App\Services\Local\Address\AddressServiceInterface;
use App\Http\Requests\Address\GetAddressForUserRequest;

class AddressController extends Controller
{
    private AddressServiceInterface $addressService;
    private UserServiceInterface $userService;

    public function __construct(AddressServiceInterface $addressService, UserServiceInterface $userService)
    {
        $this->addressService = $addressService;
        $this->userService = $userService;
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
            $filters['user_id'] = $this->userService->getCurrentUser()['id'];
            $filters['deleted_at'] = 'null';
            $page_formatting = $this->getPageFormatting($request);
            return response()->json($this->getResponse($page_formatting, $this->addressService->getAll($page_formatting, $filters), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Get a single model
     *
     * @param GetAddressForUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(GetAddressForUserRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse(
                [],
                $this->addressService->get(
                    value: $request->validated()['id'],
                    excludeRelationships: ['user']
                ), $request)
            );
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
            $data = $this->addressService->post(
                array_merge(['user_id' => $this->userService->getCurrentUser()['id']], $request->validated())
            );
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
            $data = $this->addressService->update($id, $request->validated());
            return response()->json($this->putResponse($data));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Delete a model
     *
     * @param DeleteAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteAddressRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            $this->addressService->delete($request->validated()['id']);
            return response()->json($this->deleteResponse());
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }
}
