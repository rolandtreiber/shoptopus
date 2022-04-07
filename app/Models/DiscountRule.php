<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasUUID;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property mixed $type
 * @property mixed $amount
 * @property mixed $valid_from
 * @property mixed $valid_until
 * @property mixed $products
 * @property mixed $categories
 * @property boolean $enabled
 */
class DiscountRule extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasUUID, HasTranslations, SoftDeletes, HasSlug, \OwenIt\Auditing\Auditable, HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('slug');
    }

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
        'enabled',
        'deleted_at'
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
        'enabled' => 'boolean'
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
        $today = Carbon::today();
        switch ($view) {
            case 'active':
                $query->whereDate('valid_from', '<=', $today)
                    ->whereDate('valid_until', '>=', $today)
                    ->where('enabled', 1);
                break;
            case 'not_started':
                $query->whereDate('valid_from', '>', $today);
                break;
            case 'expired':
                $query->whereDate('valid_until', '<', $today);
                break;
            case 'all_inactive':
                $query->where(function($q) use($today) {
                    $q->whereDate('valid_from', '>', $today)
                        ->orWhereDate('valid_until', '<', $today)->orWhere('enabled', 0);
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
