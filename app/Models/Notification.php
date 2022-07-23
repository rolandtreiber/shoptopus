<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends SearchableModel
{
    use HasFactory;

    protected $casts = [
        'id' => 'string',
        'data' => 'object'
    ];

    public function scopeView($query, $view)
    {
        switch ($view) {
            case NotificationType::UserSignup['type']:
                $query->where('type', NotificationType::UserSignup['className']);
                break;
            case NotificationType::ProductRunningLow['type']:
                $query->where('type', NotificationType::ProductRunningLow['className']);
                break;
            case NotificationType::ProductOutOfStock['type']:
                $query->where('type', NotificationType::ProductOutOfStock['className']);
                break;
            case NotificationType::NewOrderPlaced['type']:
                $query->where('type', NotificationType::NewOrderPlaced['className']);
                break;
        }
    }
}
