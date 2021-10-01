<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @property string $id
 * @property mixed $deliveryRules
 * @property mixed $status
 * @property mixed $enabled_by_default_on_creation
 * @property mixed $price
 */
class DeliveryType extends SearchableModel
{
    use HasFactory;
    use HasUUID;
    use HasTranslations;

    public $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'name',
        'description',
        'enabled_by_default_on_creation',
        'price'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'status' => 'integer',
        'enabled_by_default_on_creation' => 'boolean',
        'price' => 'decimal:2'
    ];

    /**
     * @return HasMany
     */
    public function deliveryRules(): HasMany
    {
        return $this->hasMany(DeliveryRule::class);
    }
}
