<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = Auth()->user();
        return NotificationResource::collection($user->notifications()->paginate(15));
    }

    /**
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        /** @var User $user */
        $user = Auth()->user();
        $user->unreadNotifications->markAsRead();
        return response()->json([]);
    }

    /**
     * @param Notification $notification
     * @return NotificationResource
     */
    public function show(Notification $notification): NotificationResource
    {
        return new NotificationResource($notification);
    }
}
