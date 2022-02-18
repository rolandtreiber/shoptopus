<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use App\Helpers\GeneralHelper;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherCode extends SearchableModel implements Auditable
{
    use HasUUID, HasFactory, HasFile, \OwenIt\Auditing\Auditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'amount',
        'code',
        'name',
        'valid_from',
        'valid_until',
        'enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'amount' => 'float',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'enabled' => 'boolean'
    ];

    protected $appends = ['value'];

    public function scopeView($query, $view)
    {
        $now = Carbon::now()->toDateTimeString();

        switch ($view) {
            case 'active':
                $query->where('valid_from', '<=', $now)
                    ->where('valid_until', '>=', $now);
                break;
            case 'not_started':
                $query->where('valid_from', '>', $now);
                break;
            case 'expired':
                $query->where('valid_until', '<', $now);
                break;
            case 'all_inactive':
                $query->where(fn($q) =>
                    $q->where('valid_from', '>', $now)
                        ->where('valid_until', '<', $now)
                );
                break;
        }
    }

    /**
     * Get the orders where the voucher code was used.
     * @return HasMany
     */
    public function orders() : HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getValueAttribute()
    {
        return GeneralHelper::getDiscountValue($this->type, $this->amount);
    }
}
