<?php

namespace App\Services\Local\Notification;

use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class NotificationService implements NotificationServiceInterface
{
    private ErrorServiceInterface $errorService;

    public function __construct(ErrorServiceInterface $errorService)
    {
        $this->errorService = $errorService;
    }

    /**
     * Get currently authenticated user's notifications
     *
     * @param  null  $userId
     *
     * @throws \Exception
     */
    public function getAllUnreadNotificationsForUser($userId = null): array
    {
        try {
            if (! $userId) {
                $userId = Auth::id();
            }

            return $userId
                ? DB::select('SELECT n.id, n.data FROM notifications as n WHERE n.read_at IS NULL AND n.notifiable_id = ?', [$userId])
                : [];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.notification.getAllUnreadNotificationsForUser'));
        }
    }

    /**
     * Mark a notification read
     *
     *
     * @throws \Exception
     */
    public function markRead(array $payload): array
    {
        try {
            DB::table('notifications')->where('id', $payload['id'])->update(['read_at' => now()]);

            return ['message' => 'Success'];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.notification.markRead'));
        }
    }
}
