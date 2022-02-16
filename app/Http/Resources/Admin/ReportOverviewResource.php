<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'stats' => $this['stats'],
            'user_signups_over_time' => $this['user_signups_over_time'],
            'orders_by_status_pie_chart_data' => $this['orders_by_status_pie_chart_data'],
            'products_by_status_pie_chart_data' => $this['products_by_status_pie_chart_data']
        ];
    }
}
