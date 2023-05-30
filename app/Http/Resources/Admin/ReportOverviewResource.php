<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'stats' => $this['stats'],
            'user_signups_over_time' => $this['user_signups_over_time'],
            'orders_by_status_pie_chart_data' => $this['orders_by_status_pie_chart_data'],
            'products_by_status_pie_chart_data' => $this['products_by_status_pie_chart_data'],
            'pending_orders' => $this['pending_orders'],
            'new_signups' => $this['new_signups'],
            'low_stock' => $this['low_stock'],
            'todays_orders' => $this['todays_orders'],
        ];
    }
}
