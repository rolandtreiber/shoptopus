<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;
    use HasUUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tinyInteger',
        'token',
        'user_id',
        'issuer_user_id',
        'expiry',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'issuer_user_id' => 'string',
        'expiry' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function issuerUser()
    {
        return $this->belongsTo(\App\User::class);
    }
}
