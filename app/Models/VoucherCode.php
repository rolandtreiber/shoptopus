<?php

namespace App\Models;

use App\Enums\AvailabilityStatus;
use App\Helpers\GeneralHelper;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\Importable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Shoptopus\ExcelImportExport\traits\HasImportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property mixed|string $code
 * @property int $type
 * @property float $amount
 * @property string $id
 * @property mixed $valid_from
 * @property mixed $valid_until
 * @property string $value
 *
 * @mixin Builder
 */
class VoucherCode extends SearchableModel implements Auditable, Exportable, Importable
{
    use HasUUID, HasFactory, HasFile, \OwenIt\Auditing\Auditable, SoftDeletes, HasSlug, HasExportable, HasImportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['value'])
            ->saveSlugsTo('slug');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'amount',
        'code',
        'name',
        'valid_from',
        'valid_until',
        'enabled',
        'deleted_at',
    ];

    protected $exportableFields = [
        'slug',
        'value',
        'type',
        'valid_from',
        'valid_until',
        'code',
        'enabled',
    ];

    protected $importableFields = [
        'amount' => [
            'description' => 'The value of the voucher code in either percentage or actual value',
            'validation' => ['numeric', 'min:0'],
        ],
        'valid_from' => [
            'description' => 'Valid from date. Format: YYYY:mm:dd',
            'validation' => ['date'],
        ],
        'valid_until' => [
            'description' => 'Valid from date. Format: YYYY:mm:dd',
            'validation' => ['date'],
        ],
        'type' => [
            'description' => '1 = percentage, 2 = actual value',
            'validation' => ['integer', 'min:1', 'max:2'],
        ],
        'enabled' => [
            'description' => '0 = disabled, 1 = enabled',
            'validation' => 'boolean',
        ],
    ];

    protected $importableRelationships = [];

    protected $exportableRelationships = [
        'orders',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'amount' => 'float',
        'type' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'enabled' => 'boolean',
    ];

    protected $appends = ['value'];

    public function scopeView($query, $view)
    {
        $now = Carbon::now()->toDateTimeString();

        switch ($view) {
            case 'active':
                $query->where('valid_from', '<=', $now)
                    ->where('valid_until', '>=', $now);
                break;
            case 'not_started':
                $query->where('valid_from', '>', $now);
                break;
            case 'expired':
                $query->where('valid_until', '<', $now);
                break;
            case 'all_inactive':
                $query->where(fn ($q) => $q->where('valid_from', '>', $now)
                        ->where('valid_until', '<', $now)
                );
                break;
            case 'enabled':
                $query->where('enabled', AvailabilityStatus::Enabled);
                break;
            case 'disabled':
                $query->where('enabled', AvailabilityStatus::Disabled);
                break;
        }
    }

    /**
     * Get the orders where the voucher code was used.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getStatusAttribute(): int
    {
        $now = Carbon::now();
        if ($this->enabled === true) {
            if ($this->valid_from < $now && $this->valid_until > $now) {
                return 1;
            }
            if ($this->valid_from > $now) { // Not yet started
                return 2;
            }
            if ($this->valid_until < $now) { // Expired
                return 3;
            }
        }

        return 0;
    }

    public function getValueAttribute(): string
    {
        return GeneralHelper::getDiscountValue($this->type, $this->amount);
    }
}
