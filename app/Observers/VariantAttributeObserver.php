<?php

namespace App\Observers;

use App\Models\VariantAttribute;
use Illuminate\Support\Facades\DB;

class VariantAttributeObserver
{
    /**
     * @param VariantAttribute $variantAttribute
     * @return void
     */
    private function updateAttributeOptionsForVariant(VariantAttribute $variantAttribute)
    {
        $variantId = $variantAttribute->product_variant_id;
        $allAttributeOptions = DB::table('product_attribute_product_variant')->where('product_variant_id', $variantId)->pluck('product_attribute_option_id')->toArray();
        DB::table('product_variants')->where('id', $variantId)->update([
            'attribute_options' => json_encode($allAttributeOptions)
        ]);
    }

    /**
     * @param VariantAttribute $variantAttribute
     * @return void
     */
    public function created(VariantAttribute $variantAttribute)
    {
        $this->updateAttributeOptionsForVariant($variantAttribute);
    }

    /**
     * @param VariantAttribute $variantAttribute
     * @return void
     */
    public function deleted(VariantAttribute $variantAttribute)
    {
        $this->updateAttributeOptionsForVariant($variantAttribute);
    }
}
