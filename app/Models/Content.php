<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contentable_type',
        'contentable_id',
        'language_id',
        'type',
        'text',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'contentable_id' => 'integer',
        'language_id' => 'integer',
        'type' => 'integer',
    ];


    public function language()
    {
        return $this->belongsTo(\App\Language::class);
    }
}
