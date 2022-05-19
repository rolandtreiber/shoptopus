<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasUUID;
use App\Traits\HasFiles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $id
 * @property mixed $ratable
 * @property User $user
 * @property integer $rating
 * @property string $language_prefix
 * @property string $description
 * @property string $title
 * @property string $ratable_type
 * @property integer $ratable_id
 * @property boolean $verified
 * @property boolean $enabled
 * @property Carbon $created_at
 */
class Rating extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasFiles, HasUUID, \OwenIt\Auditing\Auditable, SoftDeletes, HasSlug, HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['user.name', 'rating'])
            ->saveSlugsTo('slug');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ratable_type',
        'ratable_id',
        'rating',
        'description',
        'title',
        'language_prefix',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'ratable_id' => 'string',
        'ratable_type' => 'string',
        'enabled' => 'boolean',
        'verified' => 'boolean'
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
        'enabled'
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'user',
    ];

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
