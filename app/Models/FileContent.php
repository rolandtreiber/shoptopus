<?php

namespace App\Models;

use App\Enums\FileTypes;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property mixed|string $url
 * @property int|mixed $type
 * @property mixed $fileable_id
 * @property mixed $fileable_type
 * @property mixed|string $file_name
 * @method static where(string $string, $modelClass)
 */
class FileContent extends Model
{
    use HasFactory;
    use HasUUID;

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
