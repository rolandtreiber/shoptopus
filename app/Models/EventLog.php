<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    use HasFactory;
    use HasUUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'type',
        'notification',
        'user_id',
        'actioned',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'type' => 'integer',
        'notification' => 'boolean',
        'user_id' => 'string',
        'actioned' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
