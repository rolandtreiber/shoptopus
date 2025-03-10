<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasRatings;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use stdClass;

/**
 * @property string $id
 * @property string $user_id
 * @property string $order_id
 * @property Address $address
 * @property array $payment
 * @property array $products
 * @property VoucherCode|null $voucher_code
 * @property DeliveryType|null $delivery_type
 * @property array<string, float> $totals
 * @property string $slug
 * @property int $type
 * @property User $user
 * @property Order $order
 */
class Invoice extends SearchableModel implements Auditable
{
    use HasFactory;
    use HasFiles;
    use HasRatings;
    use HasUUID;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['order.slug'])
            ->saveSlugsTo('slug');
    }

    protected $casts = [
        'id' => 'string',
        'address' => 'object',
        'payment' => 'object',
        'products' => 'array',
        'voucher_code' => 'object',
        'delivery_type' => 'object',
        'totals' => 'object',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
