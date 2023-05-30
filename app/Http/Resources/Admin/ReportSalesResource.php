<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportSalesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray(Request $request): array
    {
        return [
            'stats' => $this['stats'],
            'revenue_over_time' => $this['revenue_over_time'],
            'products_breakdown' => $this['products_breakdown'],
            'totals' => $this['totals'],
        ];
    }
}
