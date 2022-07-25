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
 * @property string $user_id
 * @property string $order_id
 * @property StdClass $address
 * @property StdClass $payment
 * @property array $products
 * @property StdClass $voucher_code
 * @property StdClass $delivery_type
 * @property StdClass $totals
 * @property string $slug
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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
