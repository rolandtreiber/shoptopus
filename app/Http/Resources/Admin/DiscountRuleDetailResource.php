<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GeneralHelper;
use App\Http\Resources\Common\NoteResource;
use App\Models\DiscountRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DiscountRule
 */
class DiscountRuleDetailResource extends JsonResource
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
            'amount' => GeneralHelper::getDiscountValue($this->type, $this->amount),
            'value' => $this->amount,
            'valid_from' => Carbon::parse($this->valid_from)->format('Y-m-d H:i'),
            'valid_until' => Carbon::parse($this->valid_until)->format('Y-m-d H:i'),
            'products' => ProductListResource::collection($this->products),
            'categories' => ProductCategoryListResource::collection($this->categories),
            'notes' => NoteResource::collection($this->notes),
        ];
    }
}
