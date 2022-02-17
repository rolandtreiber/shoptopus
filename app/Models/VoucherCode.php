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
                $query->whereDate('valid_from', '>', \Illuminate\Support\Carbon::today());
                break;
            case 'expired':
                $query->whereDate('valid_until', '<', Carbon::today());
                break;
            case 'all_inactive':
                $query->where(function($q) {
                    $q->whereDate('valid_from', '>', Carbon::today())
                        ->orWhereDate('valid_until', '<', Carbon::today());
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
