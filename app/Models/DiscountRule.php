<?php

namespace App\Models;

use App\Enums\DiscountTypes;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $id
 * @property mixed $type
 * @property mixed $amount
 * @property mixed $valid_from
 * @property mixed $valid_until
 * @property mixed $products
 * @property mixed $categories
 */
class DiscountRule extends SearchableModel
{
    use HasFactory;
    use HasUUID;
    use HasTranslations;

    public $translatable = ['name'];

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
        'valid_until',
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
        'name' => 'string',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'active':
                $query->whereDate('valid_from', '<=', \Carbon\Carbon::today())
                    ->whereDate('valid_until', '>=', Carbon::today());
                break;
            case 'not_started':
                $query->whereDate('valid_from', '>', Carbon::today());
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
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    /**
     * @param $query
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        $query->where('valid_from', '<=', $now)->where('valid_until', '>=', $now);
        return $query;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $now = Carbon::now();
        if ($now >= $this->valid_from && $now <= $this->valid_until) {
            return true;
        }
        return false;
    }
}
