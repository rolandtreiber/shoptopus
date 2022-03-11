<?php

namespace App\Models;

use App\Traits\HasUUID;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends SearchableModel implements Auditable
{
    use HasFactory, SoftDeletes, HasTranslations, HasUUID, \OwenIt\Auditing\Auditable;

    public $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'image',
        'enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'enabled' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->HasMany(ProductAttributeOption::class);
    }
}
