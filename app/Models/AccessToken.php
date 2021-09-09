<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;

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
        'id' => 'integer',
        'user_id' => 'integer',
        'issuer_user_id' => 'integer',
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
