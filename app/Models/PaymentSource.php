<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PaymentSource extends Model implements Auditable, Exportable
{
    use HasFactory;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasSlug;
    use HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['user.last_name', 'name'])
            ->saveSlugsTo('slug');
    }

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'name',
        'source_id',
        'exp_month',
        'exp_year',
        'last_four',
        'brand',
        'stripe_user_id',
        'payment_method_id',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'user'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'source_id',
        'exp_month',
        'exp_year',
        'last_four',
        'brand',
        'stripe_user_id',
        'payment_method_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'payment_method_id' => 'integer',
        'exp_month' => 'encrypted',
        'exp_year' => 'encrypted',
        'last_four' => 'encrypted',
        'brand' => 'encrypted'
    ];

    /**
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
