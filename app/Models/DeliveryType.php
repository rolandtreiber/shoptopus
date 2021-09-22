<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 */
class DeliveryType extends Model
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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'status' => 'integer',
    ];


    public function deliveryRules()
    {
        return $this->hasMany(DeliveryRule::class);
    }
}
