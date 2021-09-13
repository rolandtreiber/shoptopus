<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string $url
 * @property int|mixed $type
 * @property mixed $fileable_id
 * @property mixed $fileable_type
 * @method static where(string $string, $modelClass)
 */
class FileContent extends Model
{
    use HasFactory;

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
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'fileable_id' => 'integer',
    ];
}
