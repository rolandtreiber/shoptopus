<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GeneralHelper;
use App\Models\DiscountRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DiscountRule
 */
class DiscountRuleListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->getTranslations('name'),
            'value' => GeneralHelper::getDiscountValue($this->type, $this->amount),
            'valid_from' => Carbon::parse($this->valid_from)->format('Y-m-d H:i'),
            'valid_until' => Carbon::parse($this->valid_until)->format('Y-m-d H:i'),
            'valid' => $this->isValid(),
            'enabled' => $this->enabled,
        ];
    }
}
