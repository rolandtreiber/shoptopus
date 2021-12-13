<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed $ratable
 */
class Rating extends SearchableModel implements Auditable
{
    use HasFactory;
    use HasFiles;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

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
    ];


    /**
     * @return MorphTo
     */
    public function ratable(): MorphTo
    {
        return $this->morphTo();
    }
}
