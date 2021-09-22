<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class DeliveryRule extends Model implements Auditable
{
    use HasFactory;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_type_id',
        'lat',
        'lon',
        'min_weight',
        'max_weight',
        'min_distance',
        'max_distance',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'delivery_type_id' => 'string',
        'status' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

}
