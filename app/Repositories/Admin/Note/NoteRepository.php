<?php

namespace App\Repositories\Admin\Note;

use App\Models\DeliveryType;
use App\Models\DiscountRule;
use App\Models\Note;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;

class NoteRepository implements NoteRepositoryInterface {

    public function create($data): Note
    {
        $validNoteables = [
            'user' => User::class,
            'order' => Order::class,
            'payment' => Payment::class,
            'voucher_code' => VoucherCode::class,
            'discount_rule' => DiscountRule::class,
            'delivery_type' => DeliveryType::class,
            'product' => Product::class,
            'product_variant' => ProductVariant::class,
            'product_category' => ProductCategory::class,
            'product_tag' => ProductTag::class,
            'product_attribute' => ProductAttribute::class,
            'product_attribute_option' => ProductAttributeOption::class
        ];


        $note = new Note();
        $note->user_id = Auth()->user()->id;
        $note->note = $data->note;
        $note->noteable_id = $data->noteable_id;
        $note->noteable_type = $validNoteables[$data->noteable_type];
        $note->save();
        return $note;
    }
}
