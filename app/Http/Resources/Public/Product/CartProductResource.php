<?php

namespace App\Http\Resources\Public\Product;

use App\Models\CartProduct;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

/**
 * @property CartProduct $pivot
 * @property float $price
 * @property float $final_price
 * @property string $id
 * @property boolean $virtual
 */
class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price = $this->price;
        $finalPrice = $this->final_price;
        $quantity = $this->pivot->quantity;

        $photo = null;
        if ($this->pivot->product_variant_id) {
            $productVariant = ProductVariant::find($this->pivot->product_variant_id);
            $price = $productVariant->price;
            $finalPrice = $productVariant->final_price;
            $productVariantPhoto = DB::table('product_variants')
                ->join('file_contents', 'file_contents.fileable_id', '=', 'product_variants.id')
                ->where('product_variants.id', '=', $this->pivot->product_variant_id)
                ->first();
            if ($productVariantPhoto) {
                $photo = [
                    'url' => $productVariantPhoto['url'],
                    'file_name' => $productVariantPhoto['file_name']
                ];
            } else {
                $product = DB::table('products')->where('id', '=', $this->pivot->product_id)->first();
                if ($product && $product['cover_photo']) {
                    $photo = json_decode($product['cover_photo']);
                }
            }
        } else {
            $product = DB::table('products')->where('id', '=', $this->pivot->product_id)->first();
            if ($product && $product['cover_photo']) {
                $photo = json_decode($product['cover_photo']);
            }
        }

        return [
            'id' => $this->id,
            'product_id' => $this->pivot->product_id,
            'product_variant_id' => $this->pivot->product_variant_id,
            'name' => $this->pivot->name,
            'item_original_price' => round((float) $price, 2),
            'item_final_price' =>  round((float) $finalPrice, 2),
            'subtotal_original_price' => round((float) $price, 2) * $quantity,
            'subtotal_final_price' => round((float) $finalPrice, 2) * $quantity,
            'quantity' => $quantity,
            'remaining_stock' => $this->pivot->remaining_stock,
            'in_other_carts' => $this->pivot->inOtherCarts,
            'photo' => $photo,
            'virtual' => $this->virtual
        ];
    }
}
