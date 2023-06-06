<?php

namespace App\Http\Controllers\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\DeleteAddressRequest;
use App\Http\Requests\Address\GetAddressForUserRequest;
use App\Http\Requests\Address\PatchRequest;
use App\Http\Requests\Address\PostRequest;
use App\Services\Local\Address\AddressServiceInterface;
use App\Services\Local\User\UserServiceInterface;
use Illuminate\Http\Request;

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
     */
    public function getAll(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);

            $filters['user_id'] = $this->userService->getCurrentUser()['id'];

            return response()->json($this->getResponse($page_formatting, $this->addressService->getAll($page_formatting, $filters), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get a single model
     */
    public function get(GetAddressForUserRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->addressService->get($request->validated()['id']), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Create a model
     */
    public function post(PostRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->addressService->post(
                array_merge(['user_id' => $this->userService->getCurrentUser()['id']], $request->validated())
            );

            return response()->json($this->postResponse($data));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Update a model
     */
    public function update(PatchRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->addressService->update($id, $request->validated());

            return response()->json($this->putResponse($data));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Delete a model
     */
    public function delete(DeleteAddressRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->addressService->delete($request->validated()['id']);

            return response()->json($this->deleteResponse());
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
