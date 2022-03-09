<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasRatings;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed|string[]|null $background_image
 */
class Banner extends SearchableModel implements Auditable
{
    use HasFactory;
    use HasTranslations;
    use HasFiles;
    use HasRatings;
    use HasUUID;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    use HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['title'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['title', 'description', 'button_text'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'show_button',
        'button_text',
        'button_url',
        'enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'title' => 'object',
        'description' => 'object',
        'background_image' => 'object',
        'enabled' => 'boolean',
        'total_clicks' => 'integer',
        'show_button' => 'boolean'
    ];
}
