<?php

namespace App\Models;

use App\Enums\OrderStatuses;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @property string $id
 * @property mixed $deliveryRules
 * @property mixed $status
 * @property mixed $enabled_by_default_on_creation
 * @property boolean $enabled
 * @property mixed $price
 */
class DeliveryType extends SearchableModel
{
    use HasFactory;
    use HasUUID;
    use HasTranslations;
    use SoftDeletes;
    use HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'enabled_by_default_on_creation',
        'price',
        'enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'enabled_by_default_on_creation' => 'boolean',
        'price' => 'decimal:2',
        'enabled' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function deliveryRules(): HasMany
    {
        return $this->hasMany(DeliveryRule::class);
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return int
     */
    public function getOrderCount(): int
    {
        return DB::table('orders')
            ->where('delivery_type_id', $this->id)
            ->whereIn('status', [
                OrderStatuses::Paid,
                OrderStatuses::Processing,
                OrderStatuses::Completed,
                OrderStatuses::OnHold])
            ->count();
    }

    /**
     * @return float
     */
    public function getTotalRevenue(): int
    {
        return DB::table('orders')
            ->select('delivery')
            ->where('delivery_type_id', $this->id)
            ->whereIn('status', [
                OrderStatuses::Paid,
                OrderStatuses::Processing,
                OrderStatuses::Completed,
                OrderStatuses::OnHold])
            ->sum('delivery');
    }

}
