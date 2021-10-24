<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property mixed|string $token
 * @property int|mixed $type
 * @property Carbon|mixed $expiry
 * @property string $issuer_user_id
 * @property string $user_id
 */
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
    public function issuerUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return bool
     */
    public function checkExpiry(): bool
    {
        $now = \Carbon\Carbon::now();
        $expiry = Carbon::parse($this->expiry);
        return $expiry > $now;
    }
}
