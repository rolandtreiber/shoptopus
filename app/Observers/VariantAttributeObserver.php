<?php

namespace App\Observers;

use App\Models\VariantAttribute;
use Illuminate\Support\Facades\DB;

class VariantAttributeObserver
{
    private function updateAttributeOptionsForVariant(VariantAttribute $variantAttribute): void
    {
        $variantId = $variantAttribute->product_variant_id;
        $allAttributeOptions = DB::table('product_attribute_product_variant')->where('product_variant_id', $variantId)->pluck('product_attribute_option_id')->toArray();
        DB::table('product_variants')->where('id', $variantId)->update([
            'attribute_options' => json_encode($allAttributeOptions),
        ]);
    }

    private function updateAttributeOptionsForParentProduct(VariantAttribute $variantAttribute): void
    {
        $variantId = $variantAttribute->product_variant_id;
        $allAttributeOptions = DB::table('product_attribute_product_variant')->where('product_variant_id', $variantId)->pluck('product_attribute_option_id')->toArray();
        DB::table('product_variants')->where('id', $variantId)->update([
            'attribute_options' => json_encode($allAttributeOptions),
        ]);
    }

    public function created(VariantAttribute $variantAttribute): void
    {
        $this->updateAttributeOptionsForVariant($variantAttribute);
        $this->updateAttributeOptionsForParentProduct($variantAttribute);
    }

    public function deleted(VariantAttribute $variantAttribute): void
    {
        $this->updateAttributeOptionsForVariant($variantAttribute);
        $this->updateAttributeOptionsForParentProduct($variantAttribute);
    }
}
