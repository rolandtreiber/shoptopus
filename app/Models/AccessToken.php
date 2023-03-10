<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property string $accessable_type
 * @property string $accessable_id
 * @property string $type
 * @property string $user_id
 * @property string $issuer_user_id
 */
class AccessToken extends Model
{
    use HasFactory, HasUUID;

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'token';
    }

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

    /**
     * @return MorphTo
     */
    public function accessable(): MorphTo
    {
        return $this->morphTo();
    }
}
