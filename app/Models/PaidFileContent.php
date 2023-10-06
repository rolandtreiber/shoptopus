<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $id
 * @property string|null $url
 * @property string|null $title
 * @property string|null $file_name
 * @property string|null $size
 * @property string $fileable_type
 * @property string $fileable_id
 * @property string|null $description
 * @property int $type
 */
class PaidFileContent extends Model
{
    use HasFactory, HasUUID, HasTranslations;

    public array $translatable = ['title', 'description'];

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
        'size'
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
}
