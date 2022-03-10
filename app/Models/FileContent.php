<?php

namespace App\Models;

use App\Enums\FileTypes;
use App\Enums\ProductStatus;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed|string $url
 * @property int|mixed $type
 * @property mixed $fileable_id
 * @property mixed $fileable_type
 * @property mixed|string $file_name
 * @property string $id
 * @property string $title
 * @property string $description
 * @mixin Builder
 */
class FileContent extends SearchableModel
{
    use HasFactory;
    use HasUUID;
    use HasTranslations;

    public $translatable = ['title', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'fileable_type',
        'fileable_id',
        'title',
        'url',
        'file_name',
        'description',
        'type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'fileable_id' => 'string',
    ];

    protected $hidden = [
        'fileable_id', 'fileable_type', 'created_at'
    ];

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'image':
                $query->where('type', FileTypes::Image);
                break;
            case 'video':
                $query->where('type', FileTypes::Video);
                break;
            case 'audio':
                $query->where('type', FileTypes::Audio);
                break;
            case 'pdf':
                $query->where('type', FileTypes::Pdf);
                break;
            case 'spreadsheet':
                $query->where('type', FileTypes::Spreadsheet);
                break;
            case 'textdocument':
                $query->where('type', FileTypes::TextDocument);
                break;
            case 'other':
                $query->where('type', FileTypes::Other);
                break;
        }
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeImage($query): Builder
    {
        return $query->where('type', FileTypes::Image);
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeAudio($query): Builder
    {
        return $query->where('type', FileTypes::Audio);
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeVideo($query): Builder
    {
        return $query->where('type', FileTypes::Video);
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopePdf($query): Builder
    {
        return $query->where('type', FileTypes::Pdf);
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeSpreadsheet($query): Builder
    {
        return $query->where('type', FileTypes::Spreadsheet);
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeDocument($query): Builder
    {
        return $query->where('type', FileTypes::TextDocument);
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeOther($query): Builder
    {
        return $query->where('type', FileTypes::Other);
    }

}
