<?php

namespace App\Http\Controllers\Local\Order;

use App\Enums\AccessTokenType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Local\Address\GetAddressForUserRequest;
use App\Http\Requests\Local\Address\GetOrderForUserRequest;
use App\Http\Requests\Local\Order\DownloadPaidFileRequest;
use App\Models\AccessToken;
use App\Models\PaidFileContent;
use App\Services\Local\Order\OrderServiceInterface;
use Google\Rpc\Context\AttributeContext\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    private OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param PaidFileContent $paidFileContent
     * @param DownloadPaidFileRequest $request
     * @return string[]|StreamedResponse
     */
    public function downloadPaidFile(PaidFileContent $paidFileContent, DownloadPaidFileRequest $request): array|StreamedResponse
    {
        $token = $request->token;
        /** @var AccessToken|null $accessToken */
        $accessToken = AccessToken::where([
            'token' => $token,
            'type' => AccessTokenType::PaidFileAccess,
            'accessable_type' => PaidFileContent::class,
            'accessable_id' => $paidFileContent->id
        ])->first();
        if ($accessToken !== null) {
            if ($accessToken->expiry < Carbon::now()) {
                return [
                    'status' => 'error',
                    'message' => 'Token expired'
                ];
            } else {
                return Storage::disk('paid')->download($paidFileContent->file_name);
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Something went wrong'
            ];
        }
    }

    /**
     * Get all models
     */
    public function getAll(Request $request): JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);

            $user = auth()->user();
            $filters['user_id'] = $user?->id;

            return response()->json($this->getResponse($page_formatting, $this->orderService->getAll($page_formatting, $filters, ['invoice']), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get a single model
     */
    public function get(GetOrderForUserRequest $request): JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->orderService->get($request->validated()['id'], 'id', ['user']), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

}
