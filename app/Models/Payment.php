<?php

namespace App\Models;

use App\Enums\PaymentTypes;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property string $id
 * @property mixed|string[]|null $proof
 * @property float|mixed $amount
 * @property string $user_id
 * @property mixed|string $payable_type
 * @property mixed|string $payable_id
 * @property int|mixed $status
 * @property int|mixed $type
 * @property mixed|string $description
 * @property string $created_at
 * @property User $user
 * @property string $payment_ref
 * @property string $method_ref
 */
class Payment extends SearchableModel implements Auditable, Exportable
{
    use HasFactory;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasExportable;
    use HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['payable.slug'])
            ->saveSlugsTo('slug');
    }

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'status',
        'payment_ref',
        'method_ref',
        'proof',
        'type',
        'amount',
        'description',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'user',
        'paymentSource'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payable_type',
        'payable_id',
        'payment_source_id',
        'user_id',
        'status',
        'payment_ref',
        'method_ref',
        'proof',
        'type',
        'amount',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'payable_id' => 'string',
        'payment_source_id' => 'string',
        'user_id' => 'string',
        'status' => 'integer',
        'type' => 'integer',
        'proof' => 'object'
    ];

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'payment':
                $query->where('type', PaymentTypes::Payment);
                break;
            case 'refund':
                $query->where('type', PaymentTypes::Refund);
        }
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function paymentSource(): BelongsTo
    {
        return $this->belongsTo(PaymentSource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
