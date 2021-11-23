<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasUUID;
use Carbon\Traits\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;
use Ramsey\Collection\Collection;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @method static filtered(array $array, Request $request)
 * @method static root()
 * @property mixed|string[]|null $menu_image
 * @property mixed|string[]|null $header_image
 * @property boolean $enabled
 * @property Collection $children
 * @property Date $updated_at
 */
class ProductCategory extends SearchableModel implements Auditable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    public $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'enabled',
        'parent_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'parent_id' => 'string',
        'menu_image' => 'object',
        'header_image' => 'object',
        'enabled' => 'boolean'
    ];


    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id')->whereNotNull('parent_id');
    }

    /**
     * @return BelongsToMany
     */
    public function discountRules(): BelongsToMany
    {
        return $this->belongsToMany(DiscountRule::class)->valid();
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
