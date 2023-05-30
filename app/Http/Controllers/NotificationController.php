<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    /**
     * @param  Request  $request
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = Auth()->user();

        return NotificationResource::collection(Notification::filtered([], $request)->view($request->view)->where('notifiable_type', User::class)->where('notifiable_id', $user->id)->paginate($request->paginate));
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

    public function clear(): JsonResponse
    {
        /** @var User $user */
        $user = Auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([]);
    }

    public function show(Notification $notification): NotificationResource
    {
        return new NotificationResource($notification);
    }
}
