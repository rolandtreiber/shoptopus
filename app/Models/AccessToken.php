<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessToken extends Model
{
    use HasFactory, HasUUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issuer_user_id');
    }

    /**
     * @return bool
     */
    public function hasExpired(): bool
    {
        $now = \Carbon\Carbon::now();
        $expiry = Carbon::parse($this->expiry);
        return $expiry < $now;
    }
}
