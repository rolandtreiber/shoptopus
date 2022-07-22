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

    public function latest(Request $request): AnonymousResourceCollection
    {
        $user = Auth()->user();
        $unreadCount = $user->notifications()->where('read_at', null)->count();
        // We want to show the latest 10 notifications ideally
        if ($unreadCount > 10) {
            // If there are more than 10 unread notifications, let's show them all as they are all important.
            return NotificationResource::collection($user->notifications()->where('read_at', null)->orderByDesc('created_at')->get());
        } else {
            // Otherwise, let's grab the latest 10
            return NotificationResource::collection($user->notifications()->limit(10)->orderByDesc('created_at')->get());
        }
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
