<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasRatings;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed|string[]|null $background_image
 * @property integer $total_clicks
 */
class Banner extends SearchableModel implements Auditable, Exportable
{
    use HasFactory;
    use HasTranslations;
    use HasFiles;
    use HasRatings;
    use HasUUID;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    use HasSlug;
    use HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['title'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['title', 'description', 'button_text'];

    /**
     * @var array
     */
    protected $exportableFields = [
        'title',
        'description',
        'show_button',
        'button_text',
        'button_url',
        'enabled',
        'created_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'description',
        'show_button',
        'button_text',
        'button_url',
        'enabled',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'title' => 'object',
        'description' => 'object',
        'background_image' => 'object',
        'enabled' => 'boolean',
        'total_clicks' => 'integer',
        'show_button' => 'boolean',
    ];
}
