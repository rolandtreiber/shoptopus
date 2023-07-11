<?php

namespace App\Models;

use App\Enums\FileType;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class FileContent extends SearchableModel
{
    use HasFactory, HasUUID, HasTranslations;

    public $translatable = ['title', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'url',
        'fileable_type',
        'fileable_id',
        'title',
        'url',
        'file_name',
        'description',
        'type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'fileable_id' => 'string',
    ];

    protected $hidden = [
        'fileable_id', 'fileable_type', 'created_at',
    ];

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'image':
                $query->where('type', FileType::Image);
                break;
            case 'video':
                $query->where('type', FileType::Video);
                break;
            case 'audio':
                $query->where('type', FileType::Audio);
                break;
            case 'pdf':
                $query->where('type', FileType::Pdf);
                break;
            case 'spreadsheet':
                $query->where('type', FileType::Spreadsheet);
                break;
            case 'textdocument':
                $query->where('type', FileType::TextDocument);
                break;
            case 'other':
                $query->where('type', FileType::Other);
                break;
        }
    }

    public function scopeImage($query): Builder
    {
        return $query->where('type', FileType::Image);
    }

    public function scopeAudio($query): Builder
    {
        return $query->where('type', FileType::Audio);
    }

    public function scopeVideo($query): Builder
    {
        return $query->where('type', FileType::Video);
    }

    public function scopePdf($query): Builder
    {
        return $query->where('type', FileType::Pdf);
    }

    public function scopeSpreadsheet($query): Builder
    {
        return $query->where('type', FileType::Spreadsheet);
    }

    public function scopeDocument($query): Builder
    {
        return $query->where('type', FileType::TextDocument);
    }

    public function scopeOther($query): Builder
    {
        return $query->where('type', FileType::Other);
    }
}
