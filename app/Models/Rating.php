<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasUUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property string $id
 * @property mixed $ratable
 * @property User $user
 * @property string $user_id
 * @property string $slug
 * @property int $rating
 * @property string $language_prefix
 * @property string $description
 * @property string $title
 * @property string $ratable_type
 * @property string $ratable_id
 * @property bool $verified
 * @property bool $enabled
 * @property Carbon $created_at
 */
class Rating extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasFiles, HasUUID, \OwenIt\Auditing\Auditable, SoftDeletes, HasSlug, HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['user.name', 'rating'])
            ->saveSlugsTo('slug');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ratable_type',
        'ratable_id',
        'user_id',
        'rating',
        'description',
        'title',
        'language_prefix',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'ratable_id' => 'string',
        'ratable_type' => 'string',
        'enabled' => 'boolean',
        'verified' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'rating',
        'title',
        'description',
        'language_prefix',
        'verified',
        'enabled',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'user',
    ];

    /**
     * @param $query
     * @param $view
     * @return void
     */
    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'verified':
                $query->where('verified', 1);
                break;
            case 'non_verified':
                $query->where('verified', 0);
                break;
            case 'enabled':
                $query->where('enabled', 1);
                break;
            case 'disabled':
                $query->where('enabled', 0);
        }
    }

    /**
     * @return MorphTo
     */
    public function ratable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function rated(): string
    {
        return str_replace("App\Models\\", '', $this->ratable_type);
    }
}
