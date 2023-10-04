<?php

namespace App\Http\Controllers\Order;

use App\Enums\AccessTokenType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\DownloadPaidFileRequest;
use App\Models\AccessToken;
use App\Models\PaidFileContent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
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

}
