<?php

namespace App\Models;

use App\Enums\ProductStatuses;
use App\Helpers\GeneralHelper;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property mixed|string $code
 * @property int $type
 * @property float $amount
 * @property string $id
 * @property mixed $valid_from
 * @property mixed $valid_until
 * @property string $value
 * @mixin Builder
*/
class VoucherCode extends SearchableModel implements Auditable
{
    use HasUUID;

    use HasFactory;
    use HasFile;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'amount',
        'name',
        'valid_from',
        'valid_until'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'type' => 'integer',
        'amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    protected $appends = ['value'];

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'active':
                $query->whereDate('valid_from', '<=', Carbon::today())
                    ->whereDate('valid_until', '>=', Carbon::today());
                break;
            case 'not_started':
                $query->whereDate('valid_from', '>=', Carbon::today());
                break;
            case 'expired':
                $query->whereDate('valid_until', '<=', Carbon::today());
                break;
            case 'all_inactive':
                $query->where(function($q) {
                    $q->whereDate('valid_from', '>=', Carbon::today())
                        ->orWhereDate('valid_until', '<=', Carbon::today());
                });
                break;
        }
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getValueAttribute()
    {
        return GeneralHelper::getDiscountValue($this->type, $this->amount);
    }
}
