<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 *
 * @property string $id
 * @property mixed $deliveryRules
 * @property mixed $status
 * @property mixed $enabled_by_default_on_creation
 * @property bool $enabled
 * @property mixed $price
 */
class DeliveryType extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasUUID, HasTranslations, SoftDeletes, HasSlug, HasExportable, \OwenIt\Auditing\Auditable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('slug');
    }

    public array $translatable = [
        'name', 'description',
    ];

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'name',
        'description',
        'price',
        'enabled',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'orders',
        'deliveryRules',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'enabled',
        'enabled_by_default_on_creation',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'price' => 'float',
        'enabled' => 'boolean',
        'enabled_by_default_on_creation' => 'boolean',
    ];

    public function deliveryRules(): HasMany
    {
        return $this->hasMany(DeliveryRule::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return int
     * TODO: Use OrderRepo
     */
    public function getOrderCount(): int
    {
        return DB::table('orders')
            ->where('delivery_type_id', $this->id)
            ->whereIn('status', [
                OrderStatus::Paid,
                OrderStatus::Processing,
                OrderStatus::Completed,
                OrderStatus::OnHold,
            ])
            ->count();
    }

    /**
     * @return int
     * TODO: Use OrderRepo
     */
    public function getTotalRevenue(): int
    {
        return DB::table('orders')
            ->select('delivery_cost')
            ->where('delivery_type_id', $this->id)
            ->whereIn('status', [
                OrderStatus::Paid,
                OrderStatus::Processing,
                OrderStatus::Completed,
                OrderStatus::OnHold, ])
            ->sum('delivery_cost');
    }
}
